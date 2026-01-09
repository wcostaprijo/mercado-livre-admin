<script setup lang="ts">
import { useCurrencyInput, CurrencyInputOptions } from 'vue-currency-input';

const props = defineProps({
  options: {
    type: Object as () => CurrencyInputOptions,
    default: () => ({
      autoDecimalDigits: true,
      precision: 2,
      currency: 'BRL',
      currencyDisplay: 'symbol',
      locale: 'pt-BR',
      hideCurrencySymbolOnFocus: false,
      hideGroupingSeparatorOnFocus: false,
      hideNegligibleDecimalDigitsOnFocus: false,
      accountingSign: false,
      useGrouping: true
    })
  }
});

const model = defineModel();

const { inputRef } = useCurrencyInput(props.options)
</script>

<template>
  <input
    v-model="model"
    class="rounded-md border-gray-100 shadow-sm focus:ring-2 bg-white outline-none focus:outline-none focus:border-0 focus:ring-[#8726d5] min-h-10 px-3 py-2 text-sm leading-5 text-gray-900 placeholder-gray-400 transition duration-150 ease-in-out block w-full"
    ref="inputRef"
    type="text"
  />
</template>