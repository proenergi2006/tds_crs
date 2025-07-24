<template>
  <div class="p-6 intro-y">
    <div v-if="!penawaran.id_penawaran" class="text-center py-10">
      <svg class="animate-spin h-8 w-8 mx-auto" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
      </svg>
      <p class="mt-2 text-gray-500">Memuat detail penawaran...</p>
    </div>

    <div v-else>
      <!-- Header -->
      <div class="flex items-center mb-4">
        <Button variant="outline-secondary" @click="goBack">Kembali</Button>
        <h1 class="text-2xl font-semibold ml-4">Penawaran {{ penawaran.nomor_penawaran }}</h1>
      </div>

      <!-- Info Umum -->
      <div class="bg-white shadow rounded-lg p-6 mb-6 grid grid-cols-2 gap-4 text-sm">
        <div>
          <p><strong>Customer:</strong> {{ penawaran.customer.nama_perusahaan }}</p>
          <p><strong>Cabang:</strong> {{ penawaran.cabang.nama_cabang }}</p>
          <p><strong>Masa Berlaku:</strong> {{ formatDate(penawaran.masa_berlaku) }}</p>
          <p><strong>Sampai:</strong> {{ formatDate(penawaran.sampai_dengan) }}</p>
          <p><strong>Metode:</strong> {{ penawaran.metode || '-' }}</p>
        </div>
        <div>
          <p><strong>Tipe Pembayaran:</strong> {{ penawaran.tipe_pembayaran || '-' }}</p>
          <p><strong>Order Method:</strong> {{ penawaran.order_method || '-' }}</p>
          <p><strong>Toleransi Penyusutan:</strong> {{ penawaran.toleransi_penyusutan }}%</p>
          <p><strong>Lokasi Kirim:</strong> {{ penawaran.lokasi_pengiriman || '-' }}</p>
          <p><strong>OAT:</strong> {{ formatCurrency(penawaran.oat) }} / volume</p>
          <p><strong>Diskon:</strong> {{ penawaran.discount }}%</p>
        </div>
      </div>

      <!-- Rincian Item -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Rincian Item</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
              <tr>
                <th class="px-4 py-2 text-left">Produk</th>
                <th class="px-2 py-2 text-right">Volume</th>
                <th class="px-2 py-2 text-right">Harga Tebus</th>
                <th class="px-2 py-2 text-right">Jumlah Harga</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="item in penawaran.items" :key="item.id_penawaran_item" class="hover:bg-slate-50">
                <td class="px-4 py-2">
                  {{ item.produk?.nama_produk || '-' }} —
                  {{ item.produk?.jenis?.nama || '-' }} /
                  {{ item.produk?.ukuran?.nama_ukuran || '-' }}
                  {{ item.produk?.ukuran?.satuan?.nama_satuan || '' }}
                </td>
                <td class="px-2 py-2 text-right">{{ formatNumber(item.volume_order) }}</td>
                <td class="px-2 py-2 text-right">{{ formatCurrency(item.harga_tebus) }}</td>
                <td class="px-2 py-2 text-right">{{ formatCurrency(item.jumlah_harga) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Ringkasan -->
      <div class="bg-white shadow rounded-lg p-6 mb-6 text-sm">
        <div class="flex justify-between mb-1">
          <span>Subtotal:</span>
          <span class="font-medium">{{ formatCurrency(penawaran.subtotal) }}</span>
        </div>
        <div v-if="penawaran.discount > 0" class="flex justify-between mb-1">
          <span>Diskon ({{ penawaran.discount }}%):</span>
          <span class="font-medium">-{{ formatCurrency(penawaran.subtotal - penawaran.harga_tebus_setelah_diskon) }}</span>
        </div>
        <div v-if="penawaran.discount > 0" class="flex justify-between mb-1">
          <span>Setelah Diskon:</span>
          <span class="font-medium">{{ formatCurrency(penawaran.harga_tebus_setelah_diskon) }}</span>
        </div>
        <div v-if="penawaran.oat > 0" class="flex justify-between mb-1">
          <span>Total OAT:</span>
          <span class="font-medium">{{ formatCurrency(penawaran.total_with_oat - penawaran.harga_tebus_setelah_diskon - penawaran.ppn11) }}</span>
        </div>
        <div class="flex justify-between mb-1">
          <span>PPN 11%:</span>
          <span class="font-medium">{{ formatCurrency(penawaran.ppn11) }}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-semibold">Total:</span>
          <span class="font-semibold">{{ formatCurrency(penawaran.total_with_oat) }}</span>
        </div>
      </div>

      <!-- Informasi tambahan -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium mb-2">Informasi Tambahan</h3>
        <p><strong>Perhitungan:</strong> {{ penawaran.perhitungan || '-' }}</p>
        <p><strong>Keterangan:</strong> {{ penawaran.keterangan || '-' }}</p>
        <p><strong>Catatan:</strong> {{ penawaran.catatan || '-' }}</p>
        <p><strong>Syarat & Ketentuan:</strong></p>
        <div class="pl-4 text-sm text-gray-700">{{ penawaran.syarat_ketentuan || '-' }}</div>
      </div>

      <!-- Aksi -->
      <Button variant="outline-primary" @click="preview">Preview PDF</Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRoute, useRouter } from 'vue-router';
import Button from '@/components/Base/Button';

const route = useRoute();
const router = useRouter();
const id = Number(route.params.id);
const penawaran = ref<any>({});

async function fetchPenawaran() {
  try {
    const { data } = await axios.get(`/api/penawarans/${id}`);
    penawaran.value = data;
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal memuat detail penawaran', 'error');
  }
}

async function preview() {
  console.log('Jenis penawaran:', penawaran.value.jenis_penawaran);

  const jenis = parseInt(penawaran.value.jenis_penawaran, 10);

  if (!jenis) {
    Swal.fire('Error', 'Jenis penawaran tidak diketahui', 'error');
    return;
  }

  try {
    let urlApi = '';

    if (jenis === 1) {
      urlApi = `/penawarans/${id}/preview`;
    } else if (jenis === 2) {
      urlApi = `/penawarans/${id}/preview-lubricant`;
    } else {
      Swal.fire('Error', 'Jenis penawaran tidak valid', 'error');
      return;
    }

    const response = await axios.get(urlApi, {
      responseType: 'blob'
    });

    const blob = new Blob([response.data], { type: 'application/pdf' });
    const pdfUrl = URL.createObjectURL(blob);
    window.open(pdfUrl, '_blank');
    setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
  } catch {
    Swal.fire('Error', 'Gagal membuka PDF', 'error');
  }
}
// async function preview() {
//   try {
//     const response = await axios.get(`/penawarans/${id}/preview`, {
//       responseType: 'blob'
//     });
//     const blob = new Blob([response.data], { type: 'application/pdf' });
//     const url = URL.createObjectURL(blob);
//     window.open(url, '_blank');
//     setTimeout(() => URL.revokeObjectURL(url), 60000);
//   } catch {
//     Swal.fire('Error', 'Gagal membuka PDF', 'error');
//   }
// }

function goBack() {
  router.push({ name: 'penawarans-list' });
}

function formatDate(d: string) {
  if (!d) return '-';
  return new Date(d).toLocaleDateString('id-ID', {
    day: '2-digit', month: 'long', year: 'numeric'
  });
}

function formatNumber(v: number | string = 0) {
  const n = typeof v === 'string' ? parseFloat(v) : v;
  return !isNaN(n) ? n.toLocaleString('id-ID') : '-';
}

function formatCurrency(v: number | string = 0) {
  const n = typeof v === 'string' ? parseFloat(v) : v;
  return !isNaN(n) ? `Rp. ${n.toLocaleString('id-ID')}` : '-';
}

onMounted(fetchPenawaran);
</script>
