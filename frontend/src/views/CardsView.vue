<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';
import CardModal from '../components/CardModal.vue';

const router = useRouter();
const authStore = useAuthStore();

const cards = ref([]);
const pagination = ref({});
const isLoading = ref(false);

const isModalOpen = ref(false);
const cardToEdit = ref(null);

const fetchCards = async (page = 1) => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/cards?page=${page}`);
    cards.value = response.data.data;
    pagination.value = response.data.meta;
  } catch (e) {
    if (e.response?.status === 401) {
      authStore.logout();
      router.push({ name: 'login' });
    }
  } finally {
    isLoading.value = false;
  }
};

const openCreateModal = () => {
  cardToEdit.value = null;
  isModalOpen.value = true;
};

const openEditModal = (card) => {
  cardToEdit.value = card;
  isModalOpen.value = true;
};

const deleteCard = async (id) => {
  if (confirm('Czy na pewno chcesz usunąć tę kartę?')) {
    await axios.delete(`/cards/${id}`);
    fetchCards(pagination.value.current_page);
  }
};

const handleLogout = async () => {
  await authStore.logout();
  router.push({ name: 'login' });
};

onMounted(() => {
  fetchCards();
});
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Zarządzanie Kartami Podarunkowymi</h1>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Zalogowany: <strong>{{ authStore.user?.email }}</strong></span>
        <button @click="handleLogout" class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded">
          Wyloguj
        </button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Lista Kart</h2>
        <button @click="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          + Dodaj Kartę
        </button>
      </div>

      <div v-if="isLoading" class="text-center py-8 text-gray-500">Ładowanie danych...</div>

      <div v-else-if="cards.length === 0" class="text-center py-8 text-gray-500">
        Brak kart w bazie danych. Dodaj pierwszą kartę!
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b">
              <th class="p-3 text-sm font-semibold">Numer Karty</th>
              <th class="p-3 text-sm font-semibold">PIN</th>
              <th class="p-3 text-sm font-semibold">Saldo</th>
              <th class="p-3 text-sm font-semibold">Ważność</th>
              <th class="p-3 text-sm font-semibold text-right">Akcje</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="card in cards" :key="card.id" class="border-b hover:bg-gray-50">
              <td class="p-3 font-mono text-sm">{{ card.card_number }}</td>
              <td class="p-3 font-mono text-sm">{{ card.pin }}</td>
              <td class="p-3 font-semibold text-green-700">{{ card.balance.toFixed(2) }} PLN</td>
              <td class="p-3 text-sm">{{ card.expiration_date }}</td>
              <td class="p-3 text-right space-x-2">
                <button @click="openEditModal(card)" class="text-blue-600 hover:underline text-sm">Edytuj</button>
                <button @click="deleteCard(card.id)" class="text-red-600 hover:underline text-sm">Usuń</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginacja -->
        <div v-if="pagination.last_page > 1" class="flex justify-between items-center mt-4 pt-4 border-t">
          <button
            :disabled="pagination.current_page === 1"
            @click="fetchCards(pagination.current_page - 1)"
            class="px-3 py-1 border rounded text-sm disabled:opacity-50"
          >
            Poprzednia
          </button>
          <span class="text-sm text-gray-600">
            Strona {{ pagination.current_page }} z {{ pagination.last_page }}
          </span>
          <button
            :disabled="pagination.current_page === pagination.last_page"
            @click="fetchCards(pagination.current_page + 1)"
            class="px-3 py-1 border rounded text-sm disabled:opacity-50"
          >
            Następna
          </button>
        </div>
      </div>
    </div>

    <CardModal
      :is-open="isModalOpen"
      :card-to-edit="cardToEdit"
      @close="isModalOpen = false"
      @saved="fetchCards(pagination.current_page || 1)"
    />
  </div>
</template>