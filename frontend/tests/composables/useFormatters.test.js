import { describe, it, expect, vi, beforeEach } from 'vitest'

// Mock useCurrency as a global (Nuxt auto-import)
vi.stubGlobal('useCurrency', () => ({
  currency: { value: 'usd' },
  currentCurrency: { value: { code: 'usd', symbol: '$', name: 'US Dollar' } },
}))
vi.stubGlobal('computed', (fn) => ({ get value() { return fn() } }))

const { useFormatters } = await import('../../app/composables/useFormatters.js')

describe('useFormatters', () => {
  let formatCurrency, formatLargeNumber, formatPercentage, formatSupply

  beforeEach(() => {
    const formatters = useFormatters()
    formatCurrency = formatters.formatCurrency
    formatLargeNumber = formatters.formatLargeNumber
    formatPercentage = formatters.formatPercentage
    formatSupply = formatters.formatSupply
  })

  describe('formatCurrency', () => {
    it('formats values >= 1 with 2 decimal places', () => {
      const result = formatCurrency(69000)
      expect(result).toContain('69,000.00')
    })

    it('formats values between 0.01 and 1 with 4 decimal places', () => {
      const result = formatCurrency(0.5432)
      expect(result).toContain('0.5432')
    })

    it('formats values below 0.01 with 5 decimal places', () => {
      const result = formatCurrency(0.00567)
      expect(result).toContain('0.00567')
    })

    it('formats extremely small values with 8+ decimal places', () => {
      const result = formatCurrency(0.0000065)
      expect(result).toMatch(/0\.000006/)
    })

    it('returns dash for null values', () => {
      expect(formatCurrency(null)).toBe('—')
      expect(formatCurrency(undefined)).toBe('—')
    })
  })

  describe('formatLargeNumber', () => {
    it('abbreviates trillions', () => {
      expect(formatLargeNumber(1350000000000)).toContain('1.35T')
    })

    it('abbreviates billions', () => {
      expect(formatLargeNumber(420000000000)).toContain('420.00B')
    })

    it('abbreviates millions', () => {
      expect(formatLargeNumber(25000000)).toContain('25.00M')
    })

    it('falls back to formatCurrency for small values', () => {
      const result = formatLargeNumber(1234)
      expect(result).toContain('1,234.00')
    })

    it('returns dash for null', () => {
      expect(formatLargeNumber(null)).toBe('—')
    })
  })

  describe('formatPercentage', () => {
    it('formats positive values with + sign', () => {
      expect(formatPercentage(2.34)).toBe('+2.34%')
    })

    it('formats negative values with - sign', () => {
      expect(formatPercentage(-1.5)).toBe('-1.50%')
    })

    it('formats zero as positive', () => {
      expect(formatPercentage(0)).toBe('+0.00%')
    })

    it('returns dash for null', () => {
      expect(formatPercentage(null)).toBe('—')
    })
  })

  describe('formatSupply', () => {
    it('formats with commas', () => {
      expect(formatSupply(19500000)).toBe('19,500,000')
    })

    it('rounds to nearest integer', () => {
      expect(formatSupply(21000000.7)).toBe('21,000,001')
    })

    it('returns dash for null', () => {
      expect(formatSupply(null)).toBe('—')
    })
  })
})
