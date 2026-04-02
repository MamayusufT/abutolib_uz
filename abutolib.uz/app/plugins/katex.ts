// @ts-ignore
import renderMathInElement from 'katex/dist/contrib/auto-render'
import 'katex/dist/katex.min.css'

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.directive('math', {
    mounted(el: HTMLElement) {
      renderMath(el)
    },
    updated(el: HTMLElement) {
      renderMath(el)
    }
  })

  function renderMath(el: HTMLElement) {
    renderMathInElement(el, {
      delimiters: [
        { left: '$$', right: '$$', display: true },
        { left: '$', right: '$', display: false },
        { left: '\\(', right: '\\)', display: false },
        { left: '\\[', right: '\\]', display: true }
      ],
      throwOnError: false,
      strict: false,
      trust: true
    })
  }
})