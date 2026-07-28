import { useI18n } from 'vue-i18n'

const localeMap: Record<string, string> = {
  hu: 'hu-HU',
  de: 'de-CH',
}

export function useDateFormat() {
  const { locale } = useI18n()

  const currentLocale = () => localeMap[locale.value] || 'de-CH'

  const formatTime = (dateStr: string): string => {
    if (!dateStr) return '-'
    const date = new Date(dateStr)
    return date.toLocaleTimeString(currentLocale(), {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    })
  }

  const formatDate = (dateStr: string): string => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleDateString(currentLocale(), {
      month: 'short',
      day: 'numeric',
    })
  }

  const formatDateTime = (dateStr: string): string => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    const loc = currentLocale()
    const datePart = date.toLocaleDateString(loc, {
      month: 'long',
      day: 'numeric',
    })
    const timePart = date.toLocaleTimeString(loc, {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    })
    return `${datePart}, ${timePart}`
  }

  const isToday = (dateStr: string): boolean => {
    if (!dateStr) return false
    const date = new Date(dateStr)
    const today = new Date()
    return (
      date.getDate() === today.getDate() &&
      date.getMonth() === today.getMonth() &&
      date.getFullYear() === today.getFullYear()
    )
  }

  return { formatTime, formatDate, formatDateTime, isToday }
}