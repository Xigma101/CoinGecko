import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StatItem from '../../app/components/ui/StatItem.vue'

describe('StatItem', () => {
  it('renders label and value', () => {
    const wrapper = mount(StatItem, {
      props: { label: 'Market Cap', value: '$1.35T' },
    })
    expect(wrapper.text()).toContain('Market Cap')
    expect(wrapper.text()).toContain('$1.35T')
  })

  it('renders the label in muted text', () => {
    const wrapper = mount(StatItem, {
      props: { label: 'Volume', value: '$50B' },
    })
    const label = wrapper.find('.text-muted')
    expect(label.text()).toBe('Volume')
  })

  it('renders the value in semibold white text', () => {
    const wrapper = mount(StatItem, {
      props: { label: 'Price', value: '$69,000' },
    })
    const value = wrapper.find('.font-semibold')
    expect(value.text()).toBe('$69,000')
  })
})
