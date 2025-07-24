<template>
    <div class="p-6 intro-y">
      <!-- Header -->
      <div class="flex items-center mb-4">
        <h2 class="text-lg font-medium">Jenis Produk</h2>
        <Button variant="primary" class="ml-auto" @click="openCreate">
          Add New Jenis Produk
        </Button>
      </div>
  
      <!-- Toolbar -->
      <div class="flex flex-wrap items-center mb-4 intro-y sm:flex-nowrap">
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
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <Table class="min-w-full divide-y divide-slate-200">
          <Table.Thead>
            <Table.Tr>
              <Table.Th class="w-12">No</Table.Th>
              <Table.Th>Nama</Table.Th>
              <Table.Th>Deskripsi</Table.Th>
              <Table.Th class="text-center">Status</Table.Th>
              <Table.Th class="text-center">Actions</Table.Th>
            </Table.Tr>
          </Table.Thead>
          <Table.Tbody>
            <Table.Tr
              v-for="(p, idx) in jenisProduks"
              :key="p.id_jenis"
              class="hover:bg-slate-100 transition-colors"
            >
              <Table.Td>{{ (currentPage - 1) * perPage + idx + 1 }}</Table.Td>
              <Table.Td>{{ p.nama }}</Table.Td>
              <Table.Td>{{ p.deskripsi || '-' }}</Table.Td>
              <Table.Td class="text-center">
                <span :class="p.is_active ? 'text-success' : 'text-danger'">
                  <Lucide icon="CheckSquare" class="w-4 h-4 inline-block mr-1" />
                  {{ p.is_active ? 'Active' : 'Inactive' }}
                </span>
              </Table.Td>
              <Table.Td class="text-center flex justify-center">
                <a @click.prevent="openEdit(p)" class="text-blue-600 hover:text-blue-800 mx-2">
                  <Lucide icon="Edit" class="w-5 h-5" />
                </a>
                <a @click.prevent="confirmDelete(p.id_jenis)" class="text-red-600 hover:text-red-800 mx-2">
                  <Lucide icon="Trash2" class="w-5 h-5" />
                </a>
              </Table.Td>
            </Table.Tr>
          </Table.Tbody>
        </Table>
      </div>
  
      <!-- Pagination -->
      <div class="flex justify-center mt-4 intro-y">
        <Pagination>
          <Pagination.Link :disabled="currentPage===1" @click="fetchJenisProduks(currentPage-1)">
            <Lucide icon="ChevronLeft" />
          </Pagination.Link>
          <Pagination.Link
            v-for="page in totalPages"
            :key="page"
            :active="page===currentPage"
            @click="fetchJenisProduks(page)"
          >
            {{ page }}
          </Pagination.Link>
          <Pagination.Link :disabled="currentPage===totalPages" @click="fetchJenisProduks(currentPage+1)">
            <Lucide icon="ChevronRight" />
          </Pagination.Link>
        </Pagination>
      </div>
  
      <!-- Create Modal -->
      <Dialog v-model:open="createModal">
        <Dialog.Panel class="w-96 p-6">
          <h3 class="text-lg font-medium mb-4">Add New Jenis Produk</h3>
          <FormInput v-model="createForm.nama" placeholder="Nama" class="mb-3" />
          <FormInput v-model="createForm.deskripsi" placeholder="Deskripsi" class="mb-3" />
          <FormSelect v-model="createForm.is_active" class="mb-3">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </FormSelect>
          <div class="flex justify-end space-x-2">
            <Button variant="outline-secondary" @click="createModal = false">Cancel</Button>
            <Button variant="primary" :loading="createLoading" @click="submitCreate">Create</Button>
          </div>
        </Dialog.Panel>
      </Dialog>
  
      <!-- Edit Modal -->
      <Dialog v-model:open="editModal">
        <Dialog.Panel class="w-96 p-6">
          <h3 class="text-lg font-medium mb-4">Edit Jenis Produk</h3>
          <FormInput v-model="editForm.nama" placeholder="Nama" class="mb-3" />
          <FormInput v-model="editForm.deskripsi" placeholder="Deskripsi" class="mb-3" />
          <FormSelect v-model="editForm.is_active" class="mb-3">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </FormSelect>
          <div class="flex justify-end space-x-2">
            <Button variant="outline-secondary" @click="editModal = false">Cancel</Button>
            <Button variant="primary" :loading="editLoading" @click="submitEdit">Save</Button>
          </div>
        </Dialog.Panel>
      </Dialog>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, reactive, onMounted, watch } from 'vue'
  import axios from 'axios'
  import { debounce } from 'lodash'
  import Swal from 'sweetalert2'
  
  import Button from '@/components/Base/Button'
  import Table from '@/components/Base/Table'
  import Pagination from '@/components/Base/Pagination'
  import { FormInput, FormSelect } from '@/components/Base/Form'
  import Lucide from '@/components/Base/Lucide'
  import { Dialog } from '@/components/Base/Headless'
  
  const jenisProduks = ref<any[]>([])
  const searchQuery = ref('')
  const perPage = ref(10)
  const currentPage = ref(1)
  const totalPages = ref(1)
  const loading = ref(false)
  
  const createModal = ref(false)
  const createForm = reactive({
    nama: '',
    deskripsi: '',
    is_active: true
  })
  const createLoading = ref(false)
  
  const editModal = ref(false)
  const editForm = reactive({
    id_jenis: 0,
    nama: '',
    deskripsi: '',
    is_active: true
  })
  const editLoading = ref(false)
  
  let jenisToDelete: number|null = null
  
  onMounted(() => {
    fetchJenisProduks()
  })
  
  async function fetchJenisProduks(page = 1) {
    loading.value = true
    try {
      const res = await axios.get('/api/jenis-produks', {
        params: { page, per_page: perPage.value, search: searchQuery.value || undefined }
      })
      jenisProduks.value = res.data.data
      currentPage.value = res.data.current_page
      totalPages.value = res.data.last_page
    } catch (e:any) {
      Swal.fire('Error', e.response?.data?.message || 'Gagal memuat data', 'error')
    } finally {
      loading.value = false
    }
  }
  
  watch(searchQuery, debounce(() => fetchJenisProduks(1), 300))
  watch(perPage, () => fetchJenisProduks(1))
  
  function openCreate() {
    createForm.nama = ''
    createForm.deskripsi = ''
    createForm.is_active = true
    createModal.value = true
  }
  
  async function submitCreate() {
    createLoading.value = true
    try {
      const { data } = await axios.post('/api/jenis-produks', createForm)
      jenisProduks.value.unshift(data)
      createModal.value = false
      Swal.fire({ icon:'success', title:'Data berhasil dibuat', toast:true, position:'top-end', timer:2000 })
    } catch (e:any) {
      Swal.fire('Error', e.response?.data?.message || 'Gagal membuat data', 'error')
    } finally {
      createLoading.value = false
    }
  }
  
  function openEdit(p: any) {
    editForm.id_jenis = p.id_jenis
    editForm.nama = p.nama
    editForm.deskripsi = p.deskripsi
    editForm.is_active = p.is_active
    editModal.value = true
  }
  
  async function submitEdit() {
    editLoading.value = true
    try {
      const { data } = await axios.put(`/api/jenis-produks/${editForm.id_jenis}`, editForm)
      const idx = jenisProduks.value.findIndex(r => r.id_jenis === data.id_jenis)
      if (idx !== -1) jenisProduks.value[idx] = data
      editModal.value = false
      Swal.fire({ icon:'success', title:'Data berhasil diperbarui', toast:true, position:'top-end', timer:2000 })
    } catch (e:any) {
      Swal.fire('Error', e.response?.data?.message || 'Gagal memperbarui data', 'error')
    } finally {
      editLoading.value = false
    }
  }
  
  function confirmDelete(id: number) {
    Swal.fire({
      title: 'Are you sure?',
      text: 'Data tidak bisa dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/jenis-produks/${id}`)
          jenisProduks.value = jenisProduks.value.filter(j => j.id_jenis !== id)
          Swal.fire({ icon: 'success', title: 'Data berhasil dihapus', toast: true, position: 'top-end', timer: 2000 })
        } catch (e:any) {
          Swal.fire('Error', e.response?.data?.message || 'Gagal menghapus data', 'error')
        }
      }
    })
  }
  </script>
  