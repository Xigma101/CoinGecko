import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import SortIcon from '../../app/components/ui/SortIcon.vue'

describe('SortIcon', () => {
  it('renders without errors when inactive', () => {
    const wrapper = mount(SortIcon, {
      props: { active: false, asc: true },
      global: { stubs: { ChevronUpIcon: true, ChevronDownIcon: true, ChevronUpDownIcon: true } },
    })
    expect(wrapper.exists()).toBe(true)
  })

  it('renders different content based on active/asc props', () => {
    const inactive = mount(SortIcon, {
      props: { active: false, asc: true },
      global: { stubs: { ChevronUpIcon: true, ChevronDownIcon: true, ChevronUpDownIcon: true } },
    })
    const activeAsc = mount(SortIcon, {
      props: { active: true, asc: true },
      global: { stubs: { ChevronUpIcon: true, ChevronDownIcon: true, ChevronUpDownIcon: true } },
    })
    const activeDesc = mount(SortIcon, {
      props: { active: true, asc: false },
      global: { stubs: { ChevronUpIcon: true, ChevronDownIcon: true, ChevronUpDownIcon: true } },
    })

    // Each state should produce different rendered output
    const htmls = [inactive.html(), activeAsc.html(), activeDesc.html()]
    expect(new Set(htmls).size).toBe(3)
  })
})
