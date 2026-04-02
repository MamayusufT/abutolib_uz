<template>
  <div
    class="rich-editor rounded-xl overflow-hidden border transition-all duration-200"
    :class="focused ? 'ring-2 ring-primary-500/20 border-primary-500 shadow-sm' : 'border-gray-200 dark:border-gray-700'"
  >
    <div ref="editorContainer" />
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

const config = useRuntimeConfig()
const auth = useAuthStore()

const props = defineProps<{
  modelValue: string
  uploadUrl?: string
}>()

const emit = defineEmits<{ 'update:modelValue': [value: string] }>()
const editorContainer = ref<HTMLElement | null>(null)
const focused = ref(false)
let editorInstance: any = null
let isUpdatingFromProp = false

class LaravelUploadAdapter {
  private loader: any
  private url: string
  constructor(loader: any, url: string) { this.loader = loader; this.url = url }
  upload() {
    return this.loader.file.then((file: File) => {
      const fd = new FormData()
      fd.append('image', file)
      return $fetch(this.url, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${auth.tokenValue}`,
          'Accept': 'application/json'
        },
        body: fd
      }).then((d: any) => ({ default: d.url ?? d.data?.url ?? d.path }))
    })
  }
  abort() {}
}

function attachUploadAdapter(editor: any) {
  const url = `${config.public.apiBase}${props.uploadUrl ?? '/upload/image'}`
  editor.plugins.get('FileRepository').createUploadAdapter = (loader: any) => new LaravelUploadAdapter(loader, url)
}

function loadCDN(): Promise<void> {
  return new Promise((resolve) => {
    if ((window as any).__ckReady) { resolve(); return }
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css'
    document.head.appendChild(link)
    const script = document.createElement('script')
    script.src = 'https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js'
    script.onload = () => { (window as any).__ckReady = true; resolve() }
    document.head.appendChild(script)
  })
}

async function initEditor() {
  if (!editorContainer.value) return
  const CK = (window as any).CKEDITOR
  const {
    ClassicEditor, Essentials, Bold, Italic, Strikethrough, Underline, Paragraph, Heading, 
    BlockQuote, Code, CodeBlock, List, ListProperties, Table, TableToolbar, 
    TableCellProperties, TableProperties, Image, ImageInsert, ImageToolbar, 
    ImageCaption, ImageStyle, ImageResize, ImageUpload, FileRepository, 
    AutoImage, PictureEditing, Link, Undo, HorizontalLine, RemoveFormat,
    FontSize, FontFamily, FontColor, FontBackgroundColor, Alignment, 
    Indent, IndentBlock, MediaEmbed, SourceEditing, FindAndReplace, 
    SelectAll, PageBreak, SpecialCharacters, SpecialCharactersEssentials,
  } = CK

  editorInstance = await ClassicEditor.create(editorContainer.value, {
    plugins: [
      Essentials, Bold, Italic, Strikethrough, Underline, Paragraph, Heading, 
      BlockQuote, Code, CodeBlock, List, ListProperties, Table, TableToolbar, 
      TableCellProperties, TableProperties, Image, ImageInsert, ImageToolbar, 
      ImageCaption, ImageStyle, ImageResize, ImageUpload, FileRepository, 
      AutoImage, PictureEditing, Link, Undo, HorizontalLine, RemoveFormat,
      FontSize, FontFamily, FontColor, FontBackgroundColor, Alignment, 
      Indent, IndentBlock, MediaEmbed, SourceEditing, FindAndReplace, 
      SelectAll, PageBreak, SpecialCharacters, SpecialCharactersEssentials,
    ],
    toolbar: {
      items: [
        'undo', 'redo', '|', 'heading', '|', 'sourceEditing', '|',
        'link', 'insertImage', 'mediaEmbed', 'insertTable', '|',
        'numberedList', 'bulletedList', 'outdent', 'indent', '|',
        'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
        'bold', 'italic', 'underline', 'strikethrough', 'removeFormat', '|',
        'alignment', 'blockQuote', 'code', 'codeBlock', '|', 'findAndReplace',
      ],
      shouldNotGroupWhenFull: true,
    },
    initialData: props.modelValue || '',
    placeholder: 'Matnni kiriting...',
  })

  attachUploadAdapter(editorInstance)
  editorInstance.editing.view.document.on('focus', () => { focused.value = true })
  editorInstance.editing.view.document.on('blur', () => { focused.value = false })
  editorInstance.model.document.on('change:data', () => {
    if (!isUpdatingFromProp) emit('update:modelValue', editorInstance.getData())
  })
}

onMounted(() => loadCDN().then(() => initEditor()))

watch(() => props.modelValue, (val) => {
  if (editorInstance && editorInstance.getData() !== val) {
    isUpdatingFromProp = true
    editorInstance.setData(val || '')
    isUpdatingFromProp = false
  }
})

onBeforeUnmount(() => {
  editorInstance?.destroy()
  editorInstance = null
})
</script>

<style scoped>
/* Toolbar */
:deep(.ck-editor__top .ck-toolbar) {
  background-color: var(--ck-bg-toolbar, #f9fafb) !important;
  border: none !important;
  border-bottom: 1px solid #e5e7eb !important;
  padding: 6px !important;
}

.dark :deep(.ck-editor__top .ck-toolbar) {
  background-color: #111827 !important;
  border-bottom-color: #374151 !important;
}

/* Editable Area */
:deep(.ck-editor__editable) {
  border: none !important;
  min-height: 250px;
  padding: 1.25rem !important;
  font-size: 0.875rem !important;
  line-height: 1.5;
  background-color: #ffffff !important;
  color: #111827 !important;
  box-shadow: none !important;
}

.dark :deep(.ck-editor__editable) {
  background-color: #030712 !important;
  color: #f3f4f6 !important;
}

:deep(.ck-editor__editable.ck-focused) {
  outline: none !important;
}

/* Buttons */
:deep(.ck.ck-button) {
  color: #4b5563 !important;
  cursor: pointer !important;
  border-radius: 6px !important;
}

.dark :deep(.ck.ck-button) {
  color: #9ca3af !important;
}

:deep(.ck.ck-button:hover) {
  background-color: #f3f4f6 !important;
}

.dark :deep(.ck.ck-button:hover) {
  background-color: #1f2937 !important;
}

:deep(.ck.ck-button.ck-on) {
  background-color: #22c55e !important;
  color: #ffffff !important;
}

/* Separator */
:deep(.ck.ck-toolbar__separator) {
  background-color: #e5e7eb !important;
}

.dark :deep(.ck.ck-toolbar__separator) {
  background-color: #374151 !important;
}

/* Dropdowns & Popups */
:deep(.ck-dropdown__panel),
:deep(.ck-list),
:deep(.ck-balloon-panel) {
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
}

.dark :deep(.ck-dropdown__panel),
.dark :deep(.ck-list),
.dark :deep(.ck-balloon-panel) {
  background-color: #1f2937 !important;
  border-color: #374151 !important;
  color: #f3f4f6 !important;
}

.dark :deep(.ck-list__item:hover:not(.ck-list__item_disabled)) {
  background-color: #374151 !important;
}

/* Placeholder */
:deep(.ck.ck-placeholder::before) {
  color: #9ca3af !important;
}

.dark :deep(.ck.ck-placeholder::before) {
  color: #6b7280 !important;
}
</style>