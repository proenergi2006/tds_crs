<!-- src/pages/CabangsList.vue -->
<template>
  <div class="p-6 intro-y">
    <!-- Header & Add -->
    <div class="flex items-center mb-4">
      <h2 class="text-lg font-medium">Cabang</h2>
      <Button variant="primary" class="ml-auto" @click="openCreate">
        Add New Cabang
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
            <Table.Th>Nama Cabang</Table.Th>
            <Table.Th class="text-center">Status</Table.Th>
            <Table.Th class="text-center">Actions</Table.Th>
          </Table.Tr>
        </Table.Thead>
        <Table.Tbody>
          <Table.Tr
            v-for="(c, idx) in cabangs"
            :key="c.id_cabang"
            class="hover:bg-slate-100 transition-colors"
          >
            <Table.Td class="px-4 py-3 whitespace-nowrap">
              {{ (currentPage - 1) * perPage + idx + 1 }}
            </Table.Td>
            <Table.Td class="px-4 py-3 whitespace-nowrap">
              {{ c.nama_cabang }}
            </Table.Td>
            <Table.Td class="px-4 py-3 text-center whitespace-nowrap">
              <span :class="c.is_active ? 'text-success' : 'text-danger'">
                {{ c.is_active ? 'Active' : 'Inactive' }}
              </span>
            </Table.Td>
            <Table.Td class="px-4 py-3 text-center whitespace-nowrap flex justify-center">
              <a @click.prevent="openEdit(c)" class="text-blue-600 hover:text-blue-800 mx-2">
                <Lucide icon="Edit" class="w-5 h-5"/>
              </a>
              <a @click.prevent="confirmDelete(c.id_cabang)" class="text-red-600 hover:text-red-800 mx-2">
                <Lucide icon="Trash2" class="w-5 h-5"/>
              </a>
            </Table.Td>
          </Table.Tr>
        </Table.Tbody>
      </Table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-4 intro-y">
      <Pagination>
        <Pagination.Link :disabled="currentPage===1" @click="fetchCabangs(currentPage-1)">
          <Lucide icon="ChevronLeft" />
        </Pagination.Link>
        <Pagination.Link
          v-for="page in totalPages"
          :key="page"
          :active="page===currentPage"
          @click="fetchCabangs(page)"
        >
          {{ page }}
        </Pagination.Link>
        <Pagination.Link :disabled="currentPage===totalPages" @click="fetchCabangs(currentPage+1)">
          <Lucide icon="ChevronRight" />
        </Pagination.Link>
      </Pagination>
    </div>

    <!-- Create Modal -->
    <Dialog v-model:open="createModal">
      <Dialog.Panel class="w-96 p-6">
        <h3 class="text-lg font-medium mb-4">Add New Cabang</h3>
        <p v-if="createError" class="text-red-500 mb-2">{{ createError }}</p>
        <FormInput
          v-model="createForm.nama_cabang"
          placeholder="Nama Cabang"
          class="mb-3"
        />
        <div class="flex items-center mb-4">
          <FormSelect v-model="createForm.is_active" class="mr-2">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </FormSelect>
          <FormInput
            v-model="createForm.created_by"
            placeholder="Created By"
            readonly
            class="bg-gray-100 cursor-not-allowed"
          />
        </div>
        <div class="flex justify-end space-x-2">
          <Button variant="outline-secondary" @click="createModal = false">Cancel</Button>
          <Button variant="primary" :loading="createLoading" @click="submitCreate">
            Create
          </Button>
        </div>
      </Dialog.Panel>
    </Dialog>

    <!-- Edit Modal -->
    <Dialog v-model:open="editModal">
      <Dialog.Panel class="w-96 p-6">
        <h3 class="text-lg font-medium mb-4">Edit Cabang</h3>
        <p v-if="editError" class="text-red-500 mb-2">{{ editError }}</p>
        <FormInput
          v-model="editForm.nama_cabang"
          placeholder="Nama Cabang"
          class="mb-3"
        />
        <div class="flex items-center mb-4">
          <FormSelect v-model="editForm.is_active" class="mr-2">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </FormSelect>
          <FormInput
            v-model="editForm.lastupdate_by"
            placeholder="Updated By"
            readonly
            class="bg-gray-100 cursor-not-allowed"
          />
        </div>
        <div class="flex justify-end space-x-2">
          <Button variant="outline-secondary" @click="editModal = false">Cancel</Button>
          <Button variant="primary" :loading="editLoading" @click="submitEdit">
            Save
          </Button>
        </div>
      </Dialog.Panel>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog v-model:open="deleteModal" :initialFocus="deleteButtonRef">
      <Dialog.Panel class="p-5 text-center">
        <Lucide icon="XCircle" class="w-16 h-16 mx-auto text-danger" />
        <div class="mt-5 text-3xl">Are you sure?</div>
        <div class="mt-2 text-slate-500">This cannot be undone.</div>
        <div class="px-5 pb-8 text-center">
          <Button variant="outline-secondary" class="w-24 mr-1" @click="deleteModal = false">
            Cancel
          </Button>
          <Button variant="danger" ref="deleteButtonRef" class="w-24" @click="submitDelete">
            Delete
          </Button>
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
import { useRouter } from 'vue-router'

import Button from '@/components/Base/Button'
import Table from '@/components/Base/Table'
import Pagination from '@/components/Base/Pagination'
import { FormInput, FormSelect } from '@/components/Base/Form'
import Lucide from '@/components/Base/Lucide'
import { Dialog } from '@/components/Base/Headless'

// current user untuk created_by / lastupdate_by
const currentUserName = ref('')

// list & paging
const cabangs      = ref<any[]>([])
const searchQuery  = ref('')
const perPage      = ref(10)
const currentPage  = ref(1)
const totalPages   = ref(1)
const loading      = ref(false)

// create modal state
const createModal   = ref(false)
const createForm    = reactive({
  nama_cabang: '',
  is_active:   true,
  created_by:  ''
})
const createLoading = ref(false)
const createError   = ref<string|null>(null)

// edit modal state
const editModal     = ref(false)
const editForm      = reactive({
  id_cabang:     0,
  nama_cabang:   '',
  is_active:     true,
  lastupdate_by: ''
})
const editLoading   = ref(false)
const editError     = ref<string|null>(null)

// delete modal state
const deleteModal     = ref(false)
const deleteButtonRef = ref<HTMLButtonElement|null>(null)
let cabangToDelete: number|null = null

const router = useRouter()

// ambil user lalu data
onMounted(async () => {
  try {
    const { data } = await axios.get('/api/user')
    currentUserName.value = data.name
  } catch {}
  fetchCabangs()
})

async function fetchCabangs(page = 1) {
  loading.value = true
  try {
    const res = await axios.get('/api/cabangs', {
      params: {
        page,
        per_page: perPage.value,
        search: searchQuery.value || undefined
      }
    })
    cabangs.value     = res.data.data
    currentPage.value = res.data.current_page
    totalPages.value  = res.data.last_page
  } catch (e:any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal memuat data', 'error')
  } finally {
    loading.value = false
  }
}

// search & perPage watchers
watch(searchQuery, debounce(() => fetchCabangs(1), 300))
watch(perPage, () => fetchCabangs(1))

// open create modal
function openCreate() {
  createForm.nama_cabang = ''
  createForm.is_active   = true
  createForm.created_by  = currentUserName.value
  createError.value      = null
  createModal.value      = true
}

// submit create
async function submitCreate() {
  if (!createForm.nama_cabang.trim()) {
    return Swal.fire('Error','Nama Cabang tidak boleh kosong','error')
  }
  createLoading.value = true
  try {
    const { data } = await axios.post('/api/cabangs', createForm)
    cabangs.value.unshift(data)
    createModal.value = false
    Swal.fire({ icon:'success', title:'Cabang berhasil dibuat', toast:true, position:'top-end', timer:2000 })
  } catch (e:any) {
    createError.value = e.response?.data?.message || 'Gagal membuat cabang'
  } finally {
    createLoading.value = false
  }
}

// open edit modal
function openEdit(c: any) {
  editForm.id_cabang     = c.id_cabang
  editForm.nama_cabang   = c.nama_cabang
  editForm.is_active     = c.is_active
  editForm.lastupdate_by = currentUserName.value
  editError.value        = null
  editModal.value        = true
}

// submit edit
async function submitEdit() {
  if (!editForm.nama_cabang.trim()) {
    return Swal.fire('Error','Nama Cabang tidak boleh kosong','error')
  }
  editLoading.value = true
  try {
    const { data } = await axios.put(`/api/cabangs/${editForm.id_cabang}`, {
      nama_cabang:  editForm.nama_cabang,
      is_active:    editForm.is_active,
      lastupdate_by: editForm.lastupdate_by
    })
    const idx = cabangs.value.findIndex(c => c.id_cabang === data.id_cabang)
    if (idx !== -1) cabangs.value[idx] = data
    editModal.value = false
    Swal.fire({ icon:'success', title:'Cabang berhasil diperbarui', toast:true, position:'top-end', timer:2000 })
  } catch (e:any) {
    editError.value = e.response?.data?.message || 'Gagal memperbarui cabang'
  } finally {
    editLoading.value = false
  }
}

// confirm delete
function confirmDelete(id: number) {
  cabangToDelete = id
  deleteModal.value = true
}

// submit delete
async function submitDelete() {
  if (!cabangToDelete) return
  await axios.delete(`/api/cabangs/${cabangToDelete}`)
  cabangs.value = cabangs.value.filter(c => c.id_cabang !== cabangToDelete)
  deleteModal.value = false
  Swal.fire({ icon:'success', title:'Cabang berhasil dihapus', toast:true, position:'top-end', timer:2000 })
}
</script>
