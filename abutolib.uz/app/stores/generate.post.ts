export default defineEventHandler(async (event) => {
  const formData = await readFormData(event)

  const response = await fetch('https://ai.abutolib.uz/api/quiz/generate/', {
    method: 'POST',
    body: formData,
  })

  if (!response.ok) {
    throw createError({
      statusCode: response.status,
      statusMessage: `API xatolik: ${response.statusText}`
    })
  }

  const data = await response.json()
  return data
})