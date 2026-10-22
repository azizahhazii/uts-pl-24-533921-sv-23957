<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const items = ref([]);
const form = ref({ id: null, name: '', sku: '', quantity: 0, price: 0 });
const isEdit = ref(false);
const errorMessage = ref('');

// READ: Tampil Semua Data Barang
const fetchItems = async () => {
  try {
    const res = await api.get('/items');
    items.value = res.data;
  } catch (err) {
    errorMessage.value = 'Gagal mengambil data barang';
  }
};

// CREATE & UPDATE
const saveItem = async () => {
  try {
    if (isEdit.value) {
      await api.put(`/items/${form.value.id}`, form.value);
    } else {
      await api.post('/items', form.value);
    }
    resetForm();
    fetchItems();
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan data barang';
  }
};

// Set Form untuk Edit
const editItem = (item) => {
  form.value = { ...item };
  isEdit.value = true;
};

// DELETE: Hapus Data Barang
const deleteItem = async (id) => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    try {
      await api.delete(`/items/${id}`);
      fetchItems();
    } catch (err) {
      errorMessage.value = 'Gagal menghapus data';
    }
  }
};

const resetForm = () => {
  form.value = { id: null, name: '', sku: '', quantity: 0, price: 0 };
  isEdit.value = false;
  errorMessage.value = '';
};

const handleLogout = async () => {
  try {
    await api.post('/logout');
  } catch (err) {
    // Biarkan tetap logout di frontend jika error
  } finally {
    localStorage.removeItem('token');
    router.push('/login');
  }
};

onMounted(fetchItems);
</script>

<template>
  <div style="max-width: 900px; margin: 30px auto; padding: 20px; font-family: sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
      <h2>Manajemen Stok Inventaris</h2>
      <button @click="handleLogout" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Logout
      </button>
    </div>

    <p v-if="errorMessage" style="color: red; text-align: center;">{{ errorMessage }}</p>

    <!-- Form Tambah / Edit Data -->
    <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 6px; margin: 20px 0;">
      <h3>{{ isEdit ? 'Edit Barang' : 'Tambah Barang Baru' }}</h3>
      <form @submit.prevent="saveItem" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input v-model="form.name" placeholder="Nama Barang" required style="padding: 8px; flex: 2;" />
        <input v-model="form.sku" placeholder="SKU Barang" required style="padding: 8px; flex: 1;" />
        <input v-model.number="form.quantity" type="number" placeholder="Jumlah" required style="padding: 8px; width: 80px;" />
        <input v-model.number="form.price" type="number" placeholder="Harga" required style="padding: 8px; flex: 1;" />
        <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
          {{ isEdit ? 'Update' : 'Simpan' }}
        </button>
        <button v-if="isEdit" type="button" @click="resetForm" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
          Batal
        </button>
      </form>
    </div>

    <!-- Tabel Data Barang -->
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
      <thead>
        <tr style="background: #007bff; color: white;">
          <th>ID</th>
          <th>Nama Barang</th>
          <th>SKU</th>
          <th>Stok</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.name }}</td>
          <td>{{ item.sku }}</td>
          <td>{{ item.quantity }}</td>
          <td>Rp {{ Number(item.price).toLocaleString('id-ID') }}</td>
          <td>
            <button @click="editItem(item)" style="margin-right: 5px; padding: 4px 8px; background: #ffc107; border: none; border-radius: 3px; cursor: pointer;">Edit</button>
            <button @click="deleteItem(item.id)" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;">Hapus</button>
          </td>
        </tr>
        <tr v-if="items.length === 0">
          <td colspan="6" style="text-align: center; color: #777;">Belum ada data barang.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>