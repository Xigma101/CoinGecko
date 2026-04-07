/**
 * Shared reactive currency state across all pages/components.
 * Uses Nuxt useState so it survives SSR hydration.
 */
export const CURRENCIES = [
  { code: 'usd', symbol: '$', name: 'US Dollar' },
  { code: 'eur', symbol: '€', name: 'Euro' },
  { code: 'gbp', symbol: '£', name: 'British Pound' },
  { code: 'jpy', symbol: '¥', name: 'Japanese Yen' },
  { code: 'aud', symbol: 'A$', name: 'Australian Dollar' },
  { code: 'cad', symbol: 'C$', name: 'Canadian Dollar' },
  { code: 'chf', symbol: 'Fr', name: 'Swiss Franc' },
  { code: 'cny', symbol: '¥', name: 'Chinese Yuan' },
  { code: 'krw', symbol: '₩', name: 'South Korean Won' },
  { code: 'inr', symbol: '₹', name: 'Indian Rupee' },
]

export function useCurrency() {
  const currency = useState('currency', () => 'usd')

  const currentCurrency = computed(() =>
    CURRENCIES.find((c) => c.code === currency.value)
  )

  return { currency, currentCurrency, CURRENCIES }
}
