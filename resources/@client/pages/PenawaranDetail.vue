<template>
  <div class="p-6 intro-y">
    <!-- Loading -->
    <div v-if="!penawaran.id_penawaran" class="text-center py-16">
      <svg class="animate-spin h-8 w-8 mx-auto text-slate-400" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
        <path class="opacity-75" fill="currentColor" d="M4 12a 8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
      </svg>
      <p class="mt-3 text-slate-500">Memuat detail penawaran…</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="flex items-center">
        <Button variant="outline-secondary" @click="goBack" class="inline-flex items-center gap-2">
          <!-- Icon Back -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6" />
          </svg>
          Kembali
        </Button>

        <div class="ml-4">
          <h1 class="text-2xl font-semibold leading-tight">
            Penawaran <span class="text-slate-600">{{ penawaran.nomor_penawaran }}</span>
          </h1>
        </div>

        <div class="ml-auto flex gap-2">
         

       
        </div>
      </div>

      <!-- Stepper / Progress -->
      <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between text-xs">
          <div class="flex-1 flex items-center">
            <div :class="stepDotClass(1)" />
            <span class="ml-2 font-medium">Draft</span>
          </div>
          <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(2)" />
          <div class="flex-1 flex items-center">
            <div :class="stepDotClass(2)" />
            <span class="ml-2 font-medium">Waiting BM</span>
          </div>
          <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(3)" />
          <div class="flex-1 flex items-center">
            <div :class="stepDotClass(3)" />
            <span class="ml-2 font-medium">Approved BM</span>
          </div>
          <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(4)" />
          <div class="flex-1 flex items-center">
            <div :class="stepDotClass(4)" />
            <span class="ml-2 font-medium">Approved OM</span>
          </div>
        </div>
      </div>

      <!-- Info Utama -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kiri -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium mb-4">Info Umum</h2>
          <div class="grid sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">
            <div>
              <p class="text-slate-500">Customer</p>
              <p class="font-medium">{{ penawaran.customer?.nama_perusahaan || '-' }}</p>
            </div>
            <div>
              <p class="text-slate-500">Cabang</p>
              <p class="font-medium">{{ penawaran.cabang?.nama_cabang || '-' }}</p>
            </div>
            <div>
              <p class="text-slate-500">Masa Berlaku</p>
              <p class="font-medium">{{ formatDate(penawaran.masa_berlaku) }}</p>
            </div>
            <div>
              <p class="text-slate-500">Sampai Dengan</p>
              <p class="font-medium">{{ formatDate(penawaran.sampai_dengan) }}</p>
            </div>
            <div>
              <p class="text-slate-500">Metode</p>
              <p class="font-medium">{{ penawaran.metode || '-' }}</p>
            </div>
            <div>
              <p class="text-slate-500">Tipe Pembayaran</p>
              <p class="font-medium">{{ penawaran.tipe_pembayaran || '-' }}</p>
            </div>
            <div>
              <p class="text-slate-500">Order Method</p>
              <p class="font-medium">{{ penawaran.order_method || '-' }}</p>
            </div>
            <div>
              <p class="text-slate-500">Toleransi Penyusutan</p>
              <p class="font-medium">{{ penawaran.toleransi_penyusutan ?? 0 }}%</p>
            </div>

            <!-- OAT (ditampilkan) -->
            <div>
              <p class="text-slate-500">OAT per Volume</p>
              <p class="font-medium">
                {{ formatCurrency(penawaran.oat) }} / volume
              </p>
            </div>
            

            <div class="sm:col-span-2">
              <p class="text-slate-500">Lokasi Pengiriman</p>
              <p class="font-medium">{{ penawaran.lokasi_pengiriman || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Kanan -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium mb-4">Informasi Tambahan</h2>
          <dl class="space-y-3 text-sm">
            <div>
              <dt class="text-slate-500">Perhitungan</dt>
              <dd class="font-medium whitespace-pre-line">
                {{ penawaran.perhitungan || '-' }}
              </dd>
            </div>
            <div>
              <dt class="text-slate-500">Keterangan</dt>
              <dd class="font-medium whitespace-pre-line">
                {{ penawaran.keterangan || '-' }}
              </dd>
            </div>
            <div>
              <dt class="text-slate-500">Catatan</dt>
              <dd class="font-medium whitespace-pre-line">
                {{ penawaran.catatan || '-' }}
              </dd>
            </div>
            <div>
              <dt class="text-slate-500">Syarat & Ketentuan</dt>
              <dd class="font-medium whitespace-pre-line">
                {{ penawaran.syarat_ketentuan || '-' }}
              </dd>
            </div>
          </dl>

          <div class="mt-6 grid sm:grid-cols-2 gap-3 text-sm">
            <div class="bg-slate-50 rounded-lg p-3">
              <p class="text-slate-500">Catatan Verifikasi BM</p>
              <p class="font-medium whitespace-pre-line">{{ penawaran.catatan_verifikasi || '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
              <p class="text-slate-500">Catatan Verifikasi OM</p>
              <p class="font-medium whitespace-pre-line">{{ penawaran.catatan_om || '-' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Rincian Item (Tanpa Harga) -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-medium">Rincian Item</h2>
          <div class="text-xs text-slate-500">
            Total Volume PO:
            <span class="font-semibold text-slate-700">{{ totalVolume }}</span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
              <tr>
                <th class="px-4 py-2 text-left">Produk</th>
                <th class="px-2 py-2 text-right">Volume</th>
                <!-- harga & jumlah harga DIHILANGKAN -->
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr
                v-for="item in penawaran.items"
                :key="item.id_penawaran_item"
                class="hover:bg-slate-50"
              >
                <td class="px-4 py-2">
                  <div class="font-medium">
                    {{ item.produk?.nama_produk || '-' }}
                  </div>
                  <div class="text-xs text-slate-500">
                    {{ item.produk?.jenis?.nama || '-' }} /
                    {{ item.produk?.ukuran?.nama_ukuran || '-' }}
                    {{ item.produk?.ukuran?.satuan?.nama_satuan || '' }}
                  </div>
                </td>
                <td class="px-2 py-2 text-right">
                  {{ formatNumber(item.volume_order) }}
                </td>
              </tr>
              <tr v-if="!penawaran.items?.length">
                <td colspan="2" class="px-4 py-6 text-center text-slate-500 text-sm">
                  Belum ada item.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer actions -->
      <div class="flex items-center justify-end gap-2">
        <Button
          v-if="penawaran.status === 'draft'"
          variant="primary"
          @click="ajukanPenawaran"
          class="inline-flex items-center gap-2"
        >
          <!-- Icon Send -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 2L11 13" />
            <path d="M22 2L15 22l-4-9-9-4 20-7z" />
          </svg>
          Ajukan Penawaran
        </Button>

        <Button variant="outline-primary" @click="preview" class="inline-flex items-center gap-2">
          <!-- Icon File -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <path d="M14 2v6h6" />
          </svg>
          Preview PDF
        </Button>

        <!-- Tombol Preview PDF (Indonesia & English) hanya muncul setelah approved_om -->
  <Button
    v-if="penawaran.status === 'approved_om'"
    variant="outline-primary"
    @click="previewLang('id')"
    class="inline-flex items-center gap-2"
  >
    <!-- Icon file -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <path d="M14 2v6h6" />
    </svg>
    Cetak Indonesia
  </Button>

  <Button
    v-if="penawaran.status === 'approved_om'"
    variant="outline-primary"
    @click="previewLang('en')"
    class="inline-flex items-center gap-2"
  >
    <!-- Icon file -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <path d="M14 2v6h6" />
    </svg>
    Cetak English
  </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
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

const totalVolumeNumber = computed(() => {
  const items = penawaran.value?.items || [];
  return items.reduce((s: number, it: any) => s + (Number(it?.volume_order) || 0), 0);
});
const totalVolume = computed(() => totalVolumeNumber.value.toLocaleString('id-ID'));

// Total OAT = oat (per volume) * total volume PO
const totalOATNumber = computed(() => {
  const perVol = Number(penawaran.value?.oat) || 0;
  return perVol * totalVolumeNumber.value;
});

function currentStep(): number {
  const s = penawaran.value?.status;
  if (s === 'approved_om') return 4;
  if (s === 'approved_bm') return 3;
  if (s === 'waiting_branch_manager') return 2;
  return 1;
}
function stepDotClass(step: number) {
  const done = currentStep() >= step;
  return [
    'w-3 h-3 rounded-full border',
    done ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'
  ].join(' ');
}
function stepBarClass(step: number) {
  const done = currentStep() >= step;
  return done ? 'bg-indigo-200' : 'bg-slate-200';
}

async function preview() {
  const jenis = parseInt(penawaran.value.jenis_penawaran, 10);
  if (!jenis) {
    Swal.fire('Error', 'Jenis penawaran tidak diketahui', 'error');
    return;
  }
  try {
    let urlApi = '';
    if (jenis === 1) urlApi = `/penawarans/${id}/preview`;
    else if (jenis === 2) urlApi = `/penawarans/${id}/preview-lubricant`;
    else {
      Swal.fire('Error', 'Jenis penawaran tidak valid', 'error');
      return;
    }
    const response = await axios.get(urlApi, { responseType: 'blob' });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const pdfUrl = URL.createObjectURL(blob);
    window.open(pdfUrl, '_blank');
    setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
  } catch {
    Swal.fire('Error', 'Gagal membuka PDF', 'error');
  }
}

async function ajukanPenawaran() {
  const result = await Swal.fire({
    title: 'Ajukan Penawaran?',
    text: 'Setelah diajukan, penawaran akan dikirim ke Branch Manager untuk verifikasi.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, ajukan',
    cancelButtonText: 'Batal'
  });
  if (!result.isConfirmed) return;

  try {
    await axios.patch(`/api/penawarans/${id}/ajukan`);
    Swal.fire('Berhasil', 'Penawaran telah diajukan', 'success');
    fetchPenawaran();
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal mengajukan penawaran', 'error');
  }
}

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
  const n = Number(v) || 0;
  return `Rp. ${n.toLocaleString('id-ID')}`;
}

async function previewLang(lang: 'id' | 'en') {
  try {
    const response = await axios.get(`/api/penawarans/${id}/preview`, {
      params: { lang },
      responseType: 'blob',
    });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const pdfUrl = URL.createObjectURL(blob);
    window.open(pdfUrl, '_blank');
    setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
  } catch (e) {
    Swal.fire('Error', 'Gagal membuka PDF', 'error');
  }
}


onMounted(fetchPenawaran);
</script>

<style scoped>
/* kecilin scrollbar table */
table::-webkit-scrollbar { height: 8px; }
table::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 9999px;
}
</style>
