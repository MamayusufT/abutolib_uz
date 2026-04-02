<template>
  <div class="p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
        {{ totalContributions }} ta faollik
      </h3>
      
      <select 
        v-model="selectedYear" 
        @change="fetchData"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-green-500 focus:border-green-500 block p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white outline-none cursor-pointer"
      >
        <option v-for="year in availableYears" :key="year" :value="year">
          {{ year }}-yil
        </option>
      </select>
    </div>

    <div v-if="pending" class="h-[130px] flex items-center justify-center">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
    </div>

    <div v-else class="relative">
      <div class="overflow-x-auto pb-4 custom-scrollbar">
        <calendar-heatmap
          :values="activityData"
          :end-date="endDate"
          :range-color="['#ebedf0', '#9be9a8', '#40c463', '#30a14e', '#216e39']"
          tooltip-unit="faollik"
          :max="10"
          no-data-text="Ma'lumot yo'q"
          class="min-w-[750px]"
        />
      </div>
    </div>

    <div class="mt-2 flex items-center justify-end text-[10px] text-gray-400 gap-2 font-medium">
      <span>Kam</span>
      <div class="flex gap-1">
        <div v-for="color in ['#ebedf0', '#9be9a8', '#40c463', '#30a14e', '#216e39']" 
             :key="color" :style="{ backgroundColor: color }" class="w-3 h-3 rounded-[2px]"></div>
      </div>
      <span>Ko'p</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { CalendarHeatmap } from 'vue3-calendar-heatmap'
import 'vue3-calendar-heatmap/dist/style.css'

const { $api } = useNuxtApp()
const config = useRuntimeConfig()

const availableYears = ref([])
const selectedYear = ref(new Date().getFullYear())
const activityData = ref([])
const pending = ref(false)

const endDate = computed(() => {
  const currentYear = new Date().getFullYear()
  return selectedYear.value == currentYear ? new Date() : `${selectedYear.value}-12-31`
})

const totalContributions = computed(() => {
  return activityData.value.reduce((sum, item) => sum + (parseInt(item.count) || 0), 0)
})

const fetchYears = async () => {
  try {
    const years = await $api(`${config.public.apiBase}/user-activity/years`)
    availableYears.value = years
    if (years.length > 0 && !years.includes(selectedYear.value)) {
      selectedYear.value = years[0]
    }
  } catch (error) {
    availableYears.value = [new Date().getFullYear()]
  }
}

const fetchData = async () => {
  pending.value = true
  try {
    const data = await $api(`${config.public.apiBase}/user-activity`, {
      params: { year: selectedYear.value },
    })
    activityData.value = data
  } catch (error) {
    console.error(error)
  } finally {
    pending.value = false
  }
}

onMounted(async () => {
  await fetchYears()
  await fetchData()
})
</script>

<style scoped>
:deep(.vch__wrapper) { 
  font-family: inherit !important; 
}
:deep(.vch__months__item), :deep(.vch__days__item) { 
  fill: #9ca3af; 
  font-size: 9px; 
}
:deep(.vch__legend) {
  display: none !important;
}
:deep(.vch__day__square) { 
  rx: 2px; 
}
.custom-scrollbar::-webkit-scrollbar { 
  height: 4px; 
}
.custom-scrollbar::-webkit-scrollbar-track { 
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb { 
  background-color: #e5e7eb; 
  border-radius: 10px; 
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #334155;
}
</style>