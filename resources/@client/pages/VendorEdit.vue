<template>
    <div class="py-6 bg-slate-100 min-h-screen">
      <div class="intro-y grid grid-cols-12 gap-6 mt-8">
        <div class="col-span-8">
          <div class="p-5 box">
            <h2 class="text-lg font-medium mb-4">Edit Vendor</h2>
            <p v-if="error" class="text-red-500 mb-3">{{ error }}</p>
            <div class="space-y-4">
              <div>
                <FormLabel for="nama">Nama Vendor</FormLabel>
                <FormInput
                  id="nama"
                  v-model="form.nama_vendor"
                  placeholder="Nama Vendor"
                />
              </div>
              <div>
                <FormLabel for="inisial">Inisial</FormLabel>
                <FormInput
                  id="inisial"
                  v-model="form.inisial"
                  maxlength="10"
                />
              </div>
              <div>
                <FormLabel for="catatan">Catatan</FormLabel>
                <textarea
                  id="catatan"
                  v-model="form.catatan"
                  rows="3"
                  class="w-full border rounded px-3 py-2"
                ></textarea>
              </div>
              <div class="flex items-center space-x-2">
                <FormSwitch>
                  <FormSwitch.Input type="checkbox" v-model="form.is_active"/>
                </FormSwitch>
                <span>Active</span>
              </div>
              <div>
                <FormLabel for="updater">Updated By</FormLabel>
                <FormInput
                  id="updater"
                  v-model="form.lastupdate_by"
                  readonly
                  class="bg-gray-100 cursor-not-allowed"
                />
              </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
              <Button variant="outline-secondary" @click="cancel">Cancel</Button>
              <Button variant="primary" :loading="loading" @click="submit">Update</Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { reactive, ref, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import { useAuthStore } from '@/stores/auth'
  import { FormInput, FormLabel, FormSwitch } from '@/components/Base/Form'
  import Button from '@/components/Base/Button'
  
  const router = useRouter()
  const route  = useRoute()
  const auth   = useAuthStore()
  const id     = Number(route.params.id)
  
  const form = reactive<any>({
    id_vendor:      id,
    nama_vendor:    '',
    inisial:        '',
    catatan:        '',
    is_active:      true,
    lastupdate_by:  ''
  })
  
  const loading = ref(false)
  const error   = ref('')
  
  onMounted(async () => {
    // fetch data vendor
    try {
      const { data } = await axios.get(`/api/vendors/${id}`)
      Object.assign(form, data)
      form.lastupdate_by = auth.user?.name || ''
    } catch {
      Swal.fire('Error','Gagal memuat data','error')
      router.back()
    }
  })
  
  async function submit() {
    loading.value = true
    try {
      const payload = {
        nama_vendor:   form.nama_vendor,
        inisial:       form.inisial,
        catatan:       form.catatan,
        is_active:     form.is_active,
        lastupdate_by: form.lastupdate_by,
      }
      await axios.put(`/api/vendors/${id}`, payload)
      Swal.fire({ icon:'success', title:'Vendor diperbarui', toast:true, position:'top-end', timer:1500 })
      router.push({ name: 'vendors-list' })
    } catch (e:any) {
      error.value = e.response?.data?.message || 'Gagal menyimpan'
    } finally {
      loading.value = false
    }
  }
  
  function cancel() {
    router.back()
  }
  </script>
  