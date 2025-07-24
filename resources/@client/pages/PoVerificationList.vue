<template>
    <div class="p-6 intro-y">
      <div class="flex items-center">
        <h2 class="text-lg font-medium">Daftar PO untuk Verifikasi</h2>
      </div>
  
      <!-- Toolbar -->
      <div class="flex flex-wrap items-center mt-5 intro-y sm:flex-nowrap">
        <div class="mx-auto text-slate-500">
          Page {{ currentPage }} of {{ totalPages }}
        </div>
        <FormInput
          v-model="searchQuery"
          placeholder="Search…"
          class="w-56 ml-auto pr-10 !box"
        >
          <template #icon><Lucide icon="Search" /></template>
        </FormInput>
        <FormSelect v-model="perPage" class="w-20 ml-2 !box">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="25">25</option>
        </FormSelect>
      </div>
  
      <!-- Table -->
      <div class="overflow-x-auto bg-white shadow rounded-lg mt-6">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-xs font-medium text-left uppercase">No</th>
              <th class="px-4 py-2 text-xs font-medium text-left uppercase">Nomor PO</th>
              <th class="px-4 py-2 text-xs font-medium text-left uppercase">Vendor</th>
              <th class="px-4 py-2 text-xs font-medium text-left uppercase">Terminal</th>
              <th class="px-4 py-2 text-xs font-medium text-center uppercase">Status CFO</th>
              <th class="px-4 py-2 text-xs font-medium text-center uppercase">Status CEO</th>
              <th class="px-4 py-2 text-xs font-medium text-center uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr
              v-for="(item, idx) in verificationList"
              :key="item.id_po"
              class="hover:bg-slate-100 transition-colors"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + idx + 1 }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">{{ item.nomor_po }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ item.vendor.nama_vendor }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ item.terminal.nama_terminal }}</td>
              <td class="px-4 py-3 text-center whitespace-nowrap">
                <td class="px-4 py-3 text-center whitespace-nowrap">
  <template v-if="item.cfo_result === null">
    —  
  </template>
  <template v-else-if="item.cfo_result === 1">
    <!-- icon ceklis hijau -->
    <Lucide icon="CheckCircle" class="text-green-500 w-5 h-5 mr-2" />
    <span class="text-green-500 font-medium">Verified</span>
  </template>
  
  <template v-else>
    <!-- icon silang merah -->
    <Lucide icon="XCircle" class="text-red-500 w-5 h-5 mx-auto"/>
  </template>
</td>
              </td>
              <td class="">
                <template v-if="item.ceo_result === null">
    —  
  </template>
  <template v-else-if="item.ceo_result === 1">
    <!-- icon ceklis hijau -->
    <Lucide icon="CheckCircle" class="text-green-500 w-5 h-5 mr-2" />
    <span class="text-green-500 font-medium">Verified</span>
  </template>
  
  <template v-else>
    <!-- icon silang merah -->
    <Lucide icon="XCircle" class="text-red-500 w-5 h-5 mx-auto"/>
  </template>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-center">
                <RouterLink
                  :to="{ name: 'po-verification-detail', params: { id: item.id_po } }"
                  class="text-blue-600 hover:underline mx-1"
                >Detail</RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Pagination -->
      <div class="flex justify-center mt-5 intro-y">
        <Pagination>
          <Pagination.Link
            :disabled="currentPage === 1"
            @click="fetchData(currentPage - 1)"
          ><Lucide icon="ChevronLeft"/></Pagination.Link>
          <Pagination.Link
            v-for="p in totalPages"
            :key="p"
            :active="p === currentPage"
            @click="fetchData(p)"
          >{{ p }}</Pagination.Link>
          <Pagination.Link
            :disabled="currentPage === totalPages"
            @click="fetchData(currentPage + 1)"
          ><Lucide icon="ChevronRight"/></Pagination.Link>
        </Pagination>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, watch } from 'vue';
  import axios from 'axios';
  import { debounce } from 'lodash';
  import Swal from 'sweetalert2';
  import { RouterLink } from 'vue-router';
  import Pagination from '@/components/Base/Pagination';
  import { FormInput, FormSelect } from '@/components/Base/Form';
  import Lucide from '@/components/Base/Lucide';
  
  const verificationList = ref<any[]>([]);
  const searchQuery = ref('');
  const perPage = ref(10);
  const currentPage = ref(1);
  const totalPages = ref(1);
  
  async function fetchData(page = 1) {
    try {
      const { data } = await axios.get('/api/po-verification', {
        params: {
          page,
          per_page: perPage.value,
          search: searchQuery.value || undefined,
        }
      });
      verificationList.value = data.data.filter((item: { disposisi_po: number; }) => item.disposisi_po > 0);
      currentPage.value = data.current_page;
      totalPages.value = data.last_page;
    } catch (e: any) {
      Swal.fire('Error', e.response?.data?.message || 'Gagal memuat data','error');
    }
  }
  
  onMounted(() => fetchData());
  watch(searchQuery, debounce(() => fetchData(1), 300));
  watch(perPage, () => fetchData(1));
  </script>
  