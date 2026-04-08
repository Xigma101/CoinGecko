import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ErrorMessage from '../../app/components/ui/ErrorMessage.vue'

const stubs = {
  ExclamationTriangleIcon: { template: '<span />' },
}

describe('ErrorMessage', () => {
  it('renders the error message', () => {
    const wrapper = mount(ErrorMessage, {
      props: { message: 'Something broke' },
      global: { stubs },
    })
    expect(wrapper.text()).toContain('Something broke')
  })

  it('shows a retry button', () => {
    const wrapper = mount(ErrorMessage, {
      props: { message: 'Error' },
      global: { stubs },
    })
    const button = wrapper.find('button')
    expect(button.exists()).toBe(true)
    expect(button.text()).toContain('Retry')
  })

  it('emits retry event when button is clicked', async () => {
    const wrapper = mount(ErrorMessage, {
      props: { message: 'Error' },
      global: { stubs },
    })
    await wrapper.find('button').trigger('click')
    expect(wrapper.emitted('retry')).toHaveLength(1)
  })

  it('applies inline styling when inline prop is true', () => {
    const wrapper = mount(ErrorMessage, {
      props: { message: 'Error', inline: true },
      global: { stubs },
    })
    expect(wrapper.find('.border-red-500\\/20').exists()).toBe(true)
  })
})
