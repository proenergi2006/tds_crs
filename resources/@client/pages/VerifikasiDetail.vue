<template>
    <div class="p-6 intro-y">
      <div v-if="!penawaran.id_penawaran" class="text-center py-10">
        <span class="animate-spin text-lg">⏳ Memuat data penawaran...</span>
      </div>
  
      <div v-else>
        <div class="flex items-center mb-4 justify-between">
          <div>
            <Button variant="outline-secondary" @click="goBack">← Kembali</Button>
            <h1 class="text-2xl font-semibold mt-2">Verifikasi Penawaran BM {{ penawaran.nomor_penawaran }}</h1>
          </div>
        </div>
  
        <div class="bg-white p-6 rounded shadow grid grid-cols-2 gap-6 text-sm mb-6">
  <div class="space-y-2">
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Customer</span>
      <span class="col-span-2">: {{ penawaran.customer.nama_perusahaan }}</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Cabang</span>
      <span class="col-span-2">: {{ penawaran.cabang.nama_cabang }}</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Metode</span>
      <span class="col-span-2">: {{ penawaran.metode || '-' }}</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Order Method</span>
      <span class="col-span-2">: {{ penawaran.order_method || '-' }}</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Masa Berlaku</span>
      <span class="col-span-2">: {{ formatDate(penawaran.masa_berlaku) }} - {{ formatDate(penawaran.sampai_dengan) }}</span>
    </div>
  </div>

  <div class="space-y-2">
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Tipe Pembayaran</span>
      <span class="col-span-2">: {{ penawaran.tipe_pembayaran || '-' }}</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Diskon</span>
      <span class="col-span-2">: Rp.</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">OAT</span>
      <span class="col-span-2">: {{ formatCurrency(penawaran.oat) }} / volume</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Toleransi Penyusutan</span>
      <span class="col-span-2">: {{ penawaran.toleransi_penyusutan }}%</span>
    </div>
    <div class="grid grid-cols-3">
      <span class="font-medium text-gray-600">Status</span>
      <span class="col-span-2">: <span class="capitalize text-blue-600">{{ penawaran.status }}</span></span>
    </div>
  </div>
</div>

  
        <div class="bg-white p-6 rounded shadow mb-6">
          <h3 class="font-semibold mb-2">Rincian Item</h3>
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="text-left px-4 py-2">Produk</th>
                <th class="text-right px-4 py-2">Volume</th>
                <th class="text-right px-4 py-2">Harga</th>
                <th class="text-right px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in penawaran.items" :key="item.id_penawaran_item">
                <td class="px-4 py-2">
                  {{ item.produk.nama_produk }} - 
                  {{ item.produk.jenis?.nama }} / 
                  {{ item.produk.ukuran?.nama_ukuran }} {{ item.produk.ukuran?.satuan?.nama_satuan }}
                </td>
                <td class="px-4 py-2 text-right">{{ formatNumber(item.volume_order) }}</td>
                <td class="px-4 py-2 text-right">{{ formatCurrency(item.harga_tebus) }}</td>
                <td class="px-4 py-2 text-right">{{ formatCurrency(item.jumlah_harga) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <div class="bg-white p-6 rounded shadow mb-6 text-sm border border-slate-200">
  <div class="grid grid-cols-2 gap-y-2 border-b border-slate-200 pb-2 mb-2">
    <span class="text-slate-600 font-medium">Subtotal</span>
    <span class="text-right">{{ formatCurrency(penawaran.subtotal) }}</span>

    <template v-if="penawaran.discount">
      <span class="text-slate-600 font-medium">Diskon (Rp.)</span>
      <span class="text-right text-red-500">
        -{{ formatCurrency(penawaran.subtotal - penawaran.harga_tebus_setelah_diskon) }}
      </span>
    </template>

    <span class="text-slate-600 font-medium">PPN 11%</span>
    <span class="text-right">{{ formatCurrency(penawaran.ppn11) }}</span>

    <span class="text-slate-600 font-medium">Total OAT</span>
    <span class="text-right">
      {{ formatCurrency(penawaran.total_with_oat - penawaran.harga_tebus_setelah_diskon - penawaran.ppn11) }}
    </span>
  </div>

  <div class="grid grid-cols-2 pt-2">
    <span class="font-bold text-lg text-gray-700">Total Keseluruhan</span>
    <span class="text-right font-bold text-lg text-green-600">
      {{ formatCurrency(penawaran.total_with_oat) }}
    </span>
  </div>
</div>

        <div class="bg-white p-6 rounded shadow mb-6 text-sm">
        <p><strong>Catatan Verifikasi:</strong> {{ penawaran.catatan_verifikasi || '-' }}</p>
    </div>
    <div class="flex gap-2 mt-6">
  <Button
    variant="primary"
    v-if="penawaran.status === 'waiting_branch_manager'"
    @click="verifikasi"
    class="inline-flex items-center"
  >
    <!-- Ikon centang -->
    <svg
      class="w-4 h-4 mr-2"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <path d="M20 6L9 17l-5-5" />
    </svg>
    Setujui
  </Button>

  <Button
    variant="danger"
    v-if="penawaran.status === 'waiting_branch_manager'"
    @click="tolak"
    class="inline-flex items-center"
  >
    <!-- Ikon silang -->
    <svg
      class="w-4 h-4 mr-2"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <path d="M18 6L6 18" />
      <path d="M6 6l12 12" />
    </svg>
    Tolak
  </Button>
</div>

      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import Swal from 'sweetalert2'
  import axios from 'axios'
  import Button from '@/components/Base/Button'
  
  const route = useRoute()
  const router = useRouter()
  const penawaran = ref<any>({})
  
  const id = Number(route.params.id)
  
  async function fetchPenawaran() {
    const { data } = await axios.get(`/api/penawarans/${id}`)
    penawaran.value = data
  }
  
//   async function verifikasi(action: 'approved_bm' | 'rejected_bm') {
//     const isApprove = action === 'approved_bm'
//     const result = await Swal.fire({
//       title: isApprove ? 'Setujui Penawaran?' : 'Tolak Penawaran?',
//       input: 'textarea',
//       inputLabel: 'Catatan',
//       inputPlaceholder: 'Masukkan alasan atau catatan di sini...',
//       inputAttributes: { 'aria-label': 'Catatan alasan' },
//       showCancelButton: true,
//       confirmButtonText: isApprove ? 'Setujui' : 'Tolak',
//       cancelButtonText: 'Batal',
//       preConfirm: (catatan) => catatan || 'Tanpa catatan'
//     })
  
//     if (!result.isConfirmed) return
  
//     try {
//       await axios.patch(`/api/penawarans/${id}/verifikasi`, {
//         action,
//         catatan: result.value
//       })
//       Swal.fire('Berhasil', `Penawaran telah ${isApprove ? 'disetujui' : 'ditolak'}`, 'success')
//       router.push({ name: 'penawarans-verifikasi-list' })
//     } catch (err: any) {
//       Swal.fire('Error', err.response?.data?.message || 'Terjadi kesalahan', 'error')
//     }
//   }


async function verifikasi() {
  const result = await Swal.fire({
    title: 'Verifikasi Penawaran?',
    input: 'textarea',
    inputLabel: 'Catatan Verifikasi',
    inputPlaceholder: 'Masukkan catatan jika ada...',
    inputAttributes: { 'aria-label': 'Catatan' },
    showCancelButton: true,
    confirmButtonText: 'Ya, Disetujui',
    cancelButtonText: 'Batal',
    preConfirm: (value) => value || 'Tanpa catatan'
  });

  if (!result.isConfirmed) return;

  try {
    await axios.patch(`/api/penawarans/${id}/verifikasi`, {
      catatan: result.value
    });
    Swal.fire('Berhasil', 'Penawaran disetujui', 'success');
    fetchPenawaran();
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal verifikasi penawaran', 'error');
  }
}

async function tolak() {
  const result = await Swal.fire({
    title: 'Verifikasi Penawaran?',
    input: 'textarea',
    inputLabel: 'Catatan Verifikasi',
    inputPlaceholder: 'Masukkan catatan jika ada...',
    inputAttributes: { 'aria-label': 'Catatan' },
    showCancelButton: true,
    confirmButtonText: 'Ya, Ditolak',
    cancelButtonText: 'Batal',
    preConfirm: (value) => value || 'Tanpa catatan'
  });

  if (!result.isConfirmed) return;

  try {
    await axios.patch(`/api/penawarans/${id}/tolak-bm`, {
      catatan: result.value
    });
    Swal.fire('Berhasil', 'Penawaran ditolak', 'success');
    fetchPenawaran();
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal verifikasi penawaran', 'error');
  }
}


  
  function formatDate(d: string) {
    return d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'
  }
  
  function formatCurrency(val: number | string = 0) {
    const n = typeof val === 'string' ? parseFloat(val) : val
    return isNaN(n) ? '-' : `Rp. ${n.toLocaleString('id-ID')}`
  }
  
  function formatNumber(val: number | string = 0) {
    const n = typeof val === 'string' ? parseFloat(val) : val
    return isNaN(n) ? '-' : n.toLocaleString('id-ID')
  }
  
  function goBack() {
    router.back()
  }
  
  onMounted(fetchPenawaran)
  </script>
  