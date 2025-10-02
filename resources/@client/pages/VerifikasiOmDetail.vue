<template>
  <div class="p-6 intro-y">
    <!-- Loading -->
    <div v-if="!penawaran.id_penawaran" class="text-center py-16">
      <svg class="animate-spin h-8 w-8 mx-auto text-slate-400" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
      </svg>
      <p class="mt-3 text-slate-500">Memuat detail penawaran…</p>
    </div>

    <div v-else class="space-y-6">
      <!-- HEADER (Hijau) -->
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-green-600 text-white">
        <div class="p-6 flex items-start justify-between">
          <div class="flex items-center">
           
            <div>
              <h1 class="text-2xl font-semibold leading-tight">Verifikasi Penawaran</h1>
              <p class="opacity-90">
                Nomor: <span class="font-semibold">{{ penawaran.nomor_penawaran }}</span>
              </p>
            </div>
          </div>

          <div class="hidden md:flex items-center gap-2">
            <span class="text-xs opacity-80">Terakhir diperbarui:</span>
            <span class="text-xs font-medium">
              {{ new Date().toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
            </span>
          </div>
        </div>

        <!-- Stepper -->
        <div class="px-6 pb-6">
          <div class="flex items-center justify-between text-xs">
            <div class="flex-1 flex items-center">
              <div :class="stepDotClass(1)" />
              <span class="ml-2">Draft</span>
            </div>
            <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(2)" />
            <div class="flex-1 flex items-center">
              <div :class="stepDotClass(2)" />
              <span class="ml-2">Waiting BM</span>
            </div>
            <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(3)" />
            <div class="flex-1 flex items-center">
              <div :class="stepDotClass(3)" />
              <span class="ml-2">Approved BM</span>
            </div>
            <div class="flex-1 h-0.5 mx-2" :class="stepBarClass(4)" />
            <div class="flex-1 flex items-center">
              <div :class="stepDotClass(4)" />
              <span class="ml-2">Approved OM</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol kembali + aksi -->
      <div class="flex items-center justify-between">
        <Button variant="outline-secondary" @click="goBack" class="inline-flex items-center">
          <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 18l-6-6 6-6"></path>
          </svg>
          Kembali
        </Button>

        <div class="flex gap-2">
          <!-- contoh: aksi OM -->
          <Button
            v-if="penawaran.status === 'approved_bm'"
            variant="primary"
            class="inline-flex items-center"
            @click="verifikasi"
          >
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5" />
            </svg>
            Setujui
          </Button>
          <Button
            v-if="penawaran.status === 'approved_bm'"
            variant="danger"
            class="inline-flex items-center"
            @click="tolak"
          >
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6L6 18" />
              <path d="M6 6l12 12" />
            </svg>
            Tolak
          </Button>
        </div>
      </div>

      <!-- RINGKASAN & RINGKASAN TOTAL -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ringkasan -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
          <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
          <div class="grid sm:grid-cols-2 gap-y-3 gap-x-8 text-sm">
            <div v-for="f in summaryFields" :key="f.label">
              <p class="text-slate-500">{{ f.label }}</p>
              <p class="font-medium">{{ f.value || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Ringkasan Total -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h2 class="text-lg font-semibold mb-4">Ringkasan Total</h2>
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-slate-600">Subtotal</span>
              <span class="font-medium">{{ formatCurrency(penawaran.subtotal) }}</span>
            </div>

            <div v-if="penawaran.discount" class="flex items-center justify-between">
              <span class="text-slate-600">Diskon</span>
              <span class="font-medium text-rose-600">
                - {{ formatCurrency(penawaran.subtotal - penawaran.harga_tebus_setelah_diskon) }}
              </span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-600">PPN 11%</span>
              <span class="font-medium">{{ formatCurrency(penawaran.ppn11) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-600">Total OAT</span>
              <span class="font-medium">
                {{ formatCurrency(penawaran.total_with_oat - penawaran.harga_tebus_setelah_diskon - penawaran.ppn11) }}
              </span>
            </div>

            <div class="pt-3 mt-3 border-t">
              <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium">Total Keseluruhan</span>
                <span class="text-2xl font-semibold text-emerald-600">
                  {{ formatCurrency(penawaran.total_with_oat) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Rincian Item -->
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg font-semibold">Rincian Item</h2>
          <div class="text-xs text-slate-500">
            Total Volume PO:
            <span class="font-semibold text-slate-700">{{ totalVolume }}</span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
              <tr>
                <th class="text-left px-4 py-2">Produk</th>
                <th class="text-right px-4 py-2">Volume</th>
                <th class="text-right px-4 py-2">Harga</th>
                <th class="text-right px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="item in penawaran.items" :key="item.id_penawaran_item">
                <td class="px-4 py-2">
                  <div class="font-medium">{{ item.produk?.nama_produk || '-' }}</div>
                  <div class="text-xs text-slate-500">
                    {{ item.produk?.jenis?.nama || '-' }} /
                    {{ item.produk?.ukuran?.nama_ukuran || '-' }}
                    {{ item.produk?.ukuran?.satuan?.nama_satuan || '' }}
                  </div>
                </td>
                <td class="px-4 py-2 text-right">{{ formatNumber(item.volume_order) }}</td>
                <td class="px-4 py-2 text-right">{{ formatCurrency(item.harga_tebus) }}</td>
                <td class="px-4 py-2 text-right">{{ formatCurrency(item.jumlah_harga) }}</td>
              </tr>
              <tr v-if="!penawaran.items?.length">
                <td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada item.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Catatan -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-semibold mb-2">Catatan & Keterangan</h3>
          <p class="text-sm"><span class="text-slate-500">Perhitungan:</span> {{ penawaran.perhitungan || '-' }}</p>
          <p class="text-sm"><span class="text-slate-500">Keterangan:</span> {{ penawaran.keterangan || '-' }}</p>
          <p class="text-sm"><span class="text-slate-500">Catatan:</span> {{ penawaran.catatan || '-' }}</p>
          <div class="mt-2">
            <p class="text-slate-500 text-sm mb-1">Syarat & Ketentuan:</p>
            <div class="text-sm whitespace-pre-line">{{ penawaran.syarat_ketentuan || '-' }}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-semibold mb-2">Log Verifikasi</h3>
          <p class="text-sm"><span class="text-slate-500">Catatan Verifikasi BM:</span> {{ penawaran.catatan_verifikasi || '-' }}</p>
          <p class="text-sm"><span class="text-slate-500">Catatan Verifikasi OM:</span> {{ penawaran.catatan_om || '-' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRoute, useRouter } from 'vue-router'
import Button from '@/components/Base/Button'

const route = useRoute()
const router = useRouter()
const id = Number(route.params.id)

const penawaran = ref<any>({})

async function fetchPenawaran () {
  try {
    const { data } = await axios.get(`/api/penawarans/${id}`)
    penawaran.value = data
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal memuat detail penawaran', 'error')
  }
}

const totalVolume = computed(() => {
  const items = penawaran.value?.items || []
  const t = items.reduce((s: number, it: any) => s + (Number(it?.volume_order) || 0), 0)
  return t.toLocaleString('id-ID')
})

const summaryFields = computed(() => {
  const p = penawaran.value || {}
  return [
    { label: 'Customer', value: p.customer?.nama_perusahaan },
    { label: 'Cabang', value: p.cabang?.nama_cabang },
    { label: 'Metode', value: p.metode || '-' },
    { label: 'Tipe Pembayaran', value: p.tipe_pembayaran || '-' },
    { label: 'Order Method', value: p.order_method || '-' },
    { label: 'Toleransi Penyusutan', value: (p.toleransi_penyusutan ?? 0) + '%' },
    { label: 'Masa Berlaku', value: `${formatDate(p.masa_berlaku)} s/d ${formatDate(p.sampai_dengan)}` },
    { label: 'OAT / Volume', value: formatCurrency(p.oat) },
    { label: 'Diskon (Nominal)', value: formatCurrency((p.subtotal ?? 0) - (p.harga_tebus_setelah_diskon ?? 0)) },
    { label: 'Status', value: statusLabel(p.status) }
  ]
})

function currentStep (): number {
  const s = penawaran.value?.status
  if (s === 'approved_om') return 4
  if (s === 'approved_bm') return 3
  if (s === 'waiting_branch_manager') return 2
  return 1
}
function stepDotClass (step: number) {
  const done = currentStep() >= step
  return [
    'w-3 h-3 rounded-full border',
    done ? 'bg-white border-white' : 'bg-emerald-500/20 border-white/50'
  ].join(' ')
}
function stepBarClass (step: number) {
  const done = currentStep() >= step
  return done ? 'bg-white/60' : 'bg-white/30'
}

async function verifikasi () {
  const result = await Swal.fire({
    title: 'Verifikasi Penawaran?',
    input: 'textarea',
    inputLabel: 'Catatan Verifikasi',
    inputPlaceholder: 'Masukkan catatan jika ada…',
    showCancelButton: true,
    confirmButtonText: 'Ya, Disetujui',
    cancelButtonText: 'Batal',
    preConfirm: v => v || 'Tanpa catatan'
  })
  if (!result.isConfirmed) return
  try {
    await axios.patch(`/api/penawarans/${id}/verifikasi-om`, { catatan: result.value })
    Swal.fire('Berhasil', 'Penawaran disetujui', 'success')
    fetchPenawaran()
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal verifikasi penawaran', 'error')
  }
}

async function tolak () {
  const result = await Swal.fire({
    title: 'Verifikasi Penawaran?',
    input: 'textarea',
    inputLabel: 'Catatan Verifikasi',
    inputPlaceholder: 'Masukkan catatan jika ada…',
    showCancelButton: true,
    confirmButtonText: 'Ya, Ditolak',
    cancelButtonText: 'Batal',
    preConfirm: v => v || 'Tanpa catatan'
  })
  if (!result.isConfirmed) return
  try {
    await axios.patch(`/api/penawarans/${id}/tolak-om`, { catatan: result.value })
    Swal.fire('Berhasil', 'Penawaran ditolak', 'success')
    fetchPenawaran()
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal verifikasi penawaran', 'error')
  }
}

function goBack () {
  router.back()
}

function formatDate (d?: string) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
}
function formatNumber (v: number | string = 0) {
  const n = typeof v === 'string' ? parseFloat(v) : v
  return !isNaN(n) ? n.toLocaleString('id-ID') : '-'
}
function formatCurrency (v: number | string = 0) {
  const n = typeof v === 'string' ? parseFloat(v) : v
  return !isNaN(n) ? `Rp. ${n.toLocaleString('id-ID')}` : '-'
}
function statusLabel (s: string) {
  const map: Record<string, string> = {
    draft: 'Draft',
    waiting_branch_manager: 'Menunggu BM',
    approved_bm: 'Disetujui BM',
    approved_om: 'Disetujui OM',
    rejected_bm: 'Ditolak BM',
    rejected_om: 'Ditolak OM'
  }
  return map[s] || (s || '-')
}

onMounted(fetchPenawaran)
</script>

<style scoped>
table::-webkit-scrollbar { height: 8px; }
table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
</style>
