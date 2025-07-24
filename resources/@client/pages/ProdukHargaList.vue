<!-- src/pages/ProdukHargaList.vue -->
<template>
  <div class="p-6 intro-y">
    <!-- Header & Add New -->
    <div class="flex items-center">
      <h2 class="text-lg font-medium">Basic Price</h2>
      <RouterLink
        :to="{ name: 'produk-hargas-create' }"
        class="ml-auto"
      >
        <Button variant="primary">Add New Price</Button>
      </RouterLink>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center mt-5 intro-y sm:flex-nowrap space-x-4">
      <div class="mx-auto text-slate-500">
        Page {{ currentPage }} of {{ totalPages }}
      </div>
      <!-- Filter Cabang -->
      <FormSelect v-model="filterCabang" class="w-40 !box">
        <option value="">— Semua Cabang —</option>
        <option
          v-for="c in cabangs"
          :key="c.id_cabang"
          :value="c.id_cabang"
        >{{ c.nama_cabang }}</option>
      </FormSelect>
      <!-- Filter Produk -->
      <FormSelect v-model="filterProduk" class="w-40 !box">
        <option value="">— Semua Produk —</option>
        <option
          v-for="p in produks"
          :key="p.id_produk"
          :value="p.id_produk"
        >{{ p.nama_produk }}</option>
      </FormSelect>
      <!-- Search text -->
      <FormInput
        v-model="searchQuery"
        placeholder="Search…"
        class="w-56 pr-10 !box"
      >
        <template #icon><Lucide icon="Search" /></template>
      </FormInput>
      <FormSelect v-model="perPage" class="w-20 !box">
        <option :value="5">5</option>
        <option :value="10">10</option>
        <option :value="25">25</option>
      </FormSelect>
    </div>

    <!-- Data List -->
    <div class="overflow-x-auto bg-white shadow rounded-lg mt-6">
      <table class="min-w-full divide-y divide-slate-200">
        <!-- Header -->
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">No</th>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">Periode Awal</th>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">Periode Akhir</th>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">Cabang</th>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">Produk</th>
            <th class="px-4 py-2 text-xs font-medium text-center uppercase">Price List</th>
            <th class="px-4 py-2 text-xs font-medium text-center uppercase">BM</th>
            <th class="px-4 py-2 text-xs font-medium text-left uppercase">Catatan</th>
            <th class="px-4 py-2 text-xs font-medium text-center uppercase">Actions</th>
          </tr>
        </thead>
        <!-- Body -->
        <tbody class="divide-y divide-slate-200">
          <tr
            v-for="(h, idx) in hargaList"
            :key="h.id_produk_harga"
            class="hover:bg-slate-100 transition-colors"
          >
            <td class="px-4 py-3 whitespace-nowrap">
              {{ (currentPage - 1) * perPage + idx + 1 }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              {{ formatDate(h.periode_awal) }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              {{ formatDate(h.periode_akhir) }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              {{ h.cabang?.nama_cabang || '-' }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              {{ h.produk?.nama_produk || '-' }} - Uk:
              {{ h.produk?.ukuran?.nama_ukuran || '-' }} - {{ h.produk?.ukuran?.satuan?.nama_satuan || '-' }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-right">
              {{ formatNumber(h.harga_price_list) }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-right">
              {{ formatNumber(h.harga_bm) }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              {{ h.catatan || '-' }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center flex justify-center items-center">
              <RouterLink
                :to="{ name: 'produk-hargas-edit', params: { id: h.id_produk_harga } }"
                class="text-blue-600 hover:text-blue-800 mx-2"
              >
                <Lucide icon="Edit" class="w-5 h-5"/>
              </RouterLink>
              <span class="text-slate-300">|</span>
              <button
                @click="confirmDelete(h.id_produk_harga)"
                class="text-red-600 hover:text-red-800 mx-2"
              >
                <Lucide icon="Trash2" class="w-5 h-5"/>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-5 intro-y">
      <Pagination>
        <Pagination.Link
          :disabled="currentPage===1"
          @click="fetchHarga(currentPage-1)"
        >
          <Lucide icon="ChevronLeft" />
        </Pagination.Link>
        <Pagination.Link
          v-for="page in totalPages"
          :key="page"
          :active="page===currentPage"
          @click="fetchHarga(page)"
        >
          {{ page }}
        </Pagination.Link>
        <Pagination.Link
          :disabled="currentPage===totalPages"
          @click="fetchHarga(currentPage+1)"
        >
          <Lucide icon="ChevronRight" />
        </Pagination.Link>
      </Pagination>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'
import { RouterLink, useRouter } from 'vue-router'

import Button from '@/components/Base/Button'
import Pagination from '@/components/Base/Pagination'
import { FormInput, FormSelect } from '@/components/Base/Form'
import Lucide from '@/components/Base/Lucide'

const router        = useRouter()
const hargaList     = ref<any[]>([])
const cabangs       = ref<any[]>([])
const produks       = ref<any[]>([])
const searchQuery   = ref('')
const filterCabang  = ref<string|number>('')
const filterProduk  = ref<string|number>('')
const perPage       = ref(10)
const currentPage   = ref(1)
const totalPages    = ref(1)
const loading       = ref(false)
const error         = ref<string|null>(null)

// Ambil daftar Cabang & Produk untuk dropdown filter
async function fetchDropdowns() {
  try {
    const [cRes, pRes] = await Promise.all([
      axios.get('/api/cabangs', { params: { per_page: 100 } }),
      axios.get('/api/produks', { params: { per_page: 100 } }),
    ])
    cabangs.value = cRes.data.data
    produks.value = pRes.data.data
  } catch {
    // ignore errors
  }
}

// Fetch data dengan parameter filter & paging
async function fetchHarga(page = 1) {
  loading.value = true
  error.value   = null
  try {
    const res = await axios.get('/api/produk-hargas', {
      params: {
        page,
        per_page: perPage.value,
        search: searchQuery.value || undefined,
        id_cabang: filterCabang.value || undefined,
        id_produk: filterProduk.value || undefined,
      },
    })
    hargaList.value   = res.data.data
    currentPage.value = res.data.current_page
    totalPages.value  = res.data.last_page
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Gagal memuat data'
  } finally {
    loading.value = false
  }
}

function confirmDelete(id: number) {
  Swal.fire({
    title: 'Hapus data ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus'
  }).then(async res => {
    if (res.isConfirmed) {
      await axios.delete(`/api/produk-hargas/${id}`)
      hargaList.value = hargaList.value.filter(h => h.id_produk_harga !== id)
      Swal.fire({ icon:'success', title:'Terhapus', toast:true, position:'top-end', timer:1500 })
    }
  })
}

watch(
  [searchQuery, filterCabang, filterProduk],
  debounce(() => fetchHarga(1), 300)
)
watch(perPage, () => fetchHarga(1))

onMounted(async () => {
  await fetchDropdowns()
  fetchHarga()
})

// Helpers
function formatDate(dateStr: string) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: '2-digit', month: 'long', year: 'numeric'
  })
}
function formatNumber(value: number|string = 0) {
  const num = typeof value === 'string' ? parseInt(value, 10) : value
  return isNaN(num) ? '-' : num.toLocaleString('id-ID')
}
</script>
