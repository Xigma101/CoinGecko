import { describe, it, expect, vi } from 'vitest'

// Mock Nuxt's useState and computed
vi.stubGlobal('useState', (key, init) => ({ value: init() }))
vi.stubGlobal('computed', (fn) => ({ get value() { return fn() } }))

const { useCurrency, CURRENCIES } = await import('../../app/composables/useCurrency.js')

describe('useCurrency', () => {
  it('defaults to usd', () => {
    const { currency } = useCurrency()
    expect(currency.value).toBe('usd')
  })

  it('returns the current currency object', () => {
    const { currentCurrency } = useCurrency()
    expect(currentCurrency.value.code).toBe('usd')
    expect(currentCurrency.value.symbol).toBe('$')
    expect(currentCurrency.value.name).toBe('US Dollar')
  })

  it('exports the full currencies list', () => {
    expect(CURRENCIES).toHaveLength(10)
    expect(CURRENCIES[0].code).toBe('usd')
    expect(CURRENCIES[1].code).toBe('eur')
  })

  it('each currency has code, symbol, and name', () => {
    CURRENCIES.forEach((c) => {
      expect(c).toHaveProperty('code')
      expect(c).toHaveProperty('symbol')
      expect(c).toHaveProperty('name')
    })
  })
})
