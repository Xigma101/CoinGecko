/**
 * Composable for formatting numbers, currencies, and percentages
 * consistently across the application.
 *
 * Reads the active currency from useCurrency() so all formatting
 * automatically reflects the user's selected currency.
 */
export function useFormatters() {
  const { currency, currentCurrency } = useCurrency()

  /**
   * Format a number as currency using the active currency.
   */
  function formatCurrency(value) {
    if (value == null) return '—'

    const abs = Math.abs(value)
    let decimals = 2
    if (abs > 0 && abs < 0.0000001) decimals = 10
    else if (abs < 0.00001) decimals = 8
    else if (abs < 0.01) decimals = 5
    else if (abs < 1) decimals = 4

    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency.value.toUpperCase(),
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(value)
  }

  /**
   * Format large numbers with abbreviations.
   */
  function formatLargeNumber(value) {
    if (value == null) return '—'

    const sym = currentCurrency.value.symbol
    const abs = Math.abs(value)

    if (abs >= 1e12) return `${sym}${(value / 1e12).toFixed(2)}T`
    if (abs >= 1e9) return `${sym}${(value / 1e9).toFixed(2)}B`
    if (abs >= 1e6) return `${sym}${(value / 1e6).toFixed(2)}M`

    return formatCurrency(value)
  }

  /**
   * Format a percentage with +/- sign.
   */
  function formatPercentage(value) {
    if (value == null) return '—'

    const sign = value >= 0 ? '+' : ''
    return `${sign}${value.toFixed(2)}%`
  }

  /**
   * Format a raw supply number with commas.
   */
  function formatSupply(value) {
    if (value == null) return '—'
    return new Intl.NumberFormat('en-US').format(Math.round(value))
  }

  return { formatCurrency, formatLargeNumber, formatPercentage, formatSupply }
}
