import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Card from '../../app/components/ui/Card.vue'

describe('Card', () => {
  it('renders slot content', () => {
    const wrapper = mount(Card, {
      slots: { default: '<p>Test content</p>' },
    })
    expect(wrapper.text()).toContain('Test content')
  })

  it('has the correct base classes', () => {
    const wrapper = mount(Card)
    expect(wrapper.classes()).toContain('bg-dark-light')
    expect(wrapper.classes()).toContain('rounded-lg')
    expect(wrapper.classes()).toContain('border')
  })

  it('passes additional classes via attrs', () => {
    const wrapper = mount(Card, {
      attrs: { class: 'lg:col-span-2' },
    })
    expect(wrapper.classes()).toContain('lg:col-span-2')
  })
})
