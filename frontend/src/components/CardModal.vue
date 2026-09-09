<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  isOpen: Boolean,
  cardToEdit: Object,
});

const emit = defineEmits(['close', 'saved']);

const form = ref({
  card_number: '',
  pin: '',
  activation_date: '',
  expiration_date: '',
  balance: 0,
});

const errors = ref({});
const isLoading = ref(false);

watch(
  () => props.cardToEdit,
  (newVal) => {
    if (newVal) {
      form.value = { ...newVal };
    } else {
      form.value = {
        card_number: '',
        pin: '',
        activation_date: new Date().toISOString().slice(0, 16),
        expiration_date: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
        balance: 100,
      };
    }
    errors.value = {};
  },
  { immediate: true }
);

const saveCard = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    if (props.cardToEdit?.id) {
      await axios.put(`/cards/${props.cardToEdit.id}`, form.value);
    } else {
      await axios.post('/cards', form.value);
    }
    emit('saved');
    emit('close');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
      <h3 class="text-lg font-bold mb-4">
        {{ cardToEdit ? 'Edycja Karty Podarunkowej' : 'Nowa Karta Podarunkowa' }}
      </h3>

      <form @submit.prevent="saveCard" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Numer Karty (20 cyfr)</label>
          <input
            v-model="form.card_number"
            type="text"
            maxlength="20"
            class="w-full border rounded p-2 mt-1"
            :class="{ 'border-red-500': errors.card_number }"
          />
          <p v-if="errors.card_number" class="text-red-500 text-xs mt-1">{{ errors.card_number[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">PIN (4 cyfry)</label>
          <input
            v-model="form.pin"
            type="text"
            maxlength="4"
            class="w-full border rounded p-2 mt-1"
            :class="{ 'border-red-500': errors.pin }"
          />
          <p v-if="errors.pin" class="text-red-500 text-xs mt-1">{{ errors.pin[0] }}</p>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-sm font-medium text-gray-700">Data Aktywacji</label>
            <input
              v-model="form.activation_date"
              type="datetime-local"
              class="w-full border rounded p-2 mt-1"
              :class="{ 'border-red-500': errors.activation_date }"
            />
            <p v-if="errors.activation_date" class="text-red-500 text-xs mt-1">{{ errors.activation_date[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Data Ważności</label>
            <input
              v-model="form.expiration_date"
              type="date"
              class="w-full border rounded p-2 mt-1"
              :class="{ 'border-red-500': errors.expiration_date }"
            />
            <p v-if="errors.expiration_date" class="text-red-500 text-xs mt-1">{{ errors.expiration_date[0] }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Saldo (PLN)</label>
          <input
            v-model="form.balance"
            type="number"
            step="0.01"
            min="0"
            class="w-full border rounded p-2 mt-1"
            :class="{ 'border-red-500': errors.balance }"
          />
          <p v-if="errors.balance" class="text-red-500 text-xs mt-1">{{ errors.balance[0] }}</p>
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button
            type="button"
            @click="emit('close')"
            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100"
          >
            Anuluj
          </button>
          <button
            type="submit"
            :disabled="isLoading"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
          >
            {{ isLoading ? 'Zapisywanie...' : 'Zapisz' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>