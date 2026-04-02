export interface TopicForm {
  id?: number
  name: string
  description: string
  order: number
  time_limit: number | null
  is_active: boolean
}

export interface SubjectForm {
  name: string
  description: string
  color: string
  order: number
  is_active: boolean
}

export function useSubjectForm() {
  const form = reactive<SubjectForm>({
    name: '',
    description: '',
    color: '#16a34a',
    order: 0,
    is_active: true,
  })

  const topics = ref<TopicForm[]>([])
  const newFiles = ref<File[]>([])
  const deletedFileIds = ref<number[]>([])
  const imageFile = ref<File | null>(null)
  const imagePreview = ref<string | null>(null)

  function addTopic() {
    topics.value.push({
      name: '',
      description: '',
      order: topics.value.length,
      time_limit: null,
      is_active: true,
    })
  }

  function removeTopic(index: number) {
    topics.value.splice(index, 1)
  }

  function onImageChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }

  function buildFormData(deletedTopicIds: number[] = []): FormData {
    const fd = new FormData()

    fd.append('name', form.name)
    fd.append('description', form.description)
    fd.append('color', form.color)
    fd.append('order', String(form.order))
    fd.append('is_active', form.is_active ? '1' : '0')

    if (imageFile.value) {
      fd.append('image', imageFile.value)
    }

    topics.value.forEach((topic, i) => {
      if (topic.id) fd.append(`topics[${i}][id]`, String(topic.id))
      fd.append(`topics[${i}][name]`, topic.name)
      fd.append(`topics[${i}][description]`, topic.description)
      fd.append(`topics[${i}][order]`, String(topic.order))
      fd.append(`topics[${i}][is_active]`, topic.is_active ? '1' : '0')
      if (topic.time_limit !== null) {
        fd.append(`topics[${i}][time_limit]`, String(topic.time_limit))
      }
    })

    newFiles.value.forEach((file) => {
      fd.append('files[]', file)
    })

    deletedTopicIds.forEach((id) => {
      fd.append('deleted_topic_ids[]', String(id))
    })

    deletedFileIds.value.forEach((id) => {
      fd.append('deleted_file_ids[]', String(id))
    })

    return fd
  }

  return {
    form,
    topics,
    newFiles,
    deletedFileIds,
    imageFile,
    imagePreview,
    addTopic,
    removeTopic,
    onImageChange,
    buildFormData,
  }
}