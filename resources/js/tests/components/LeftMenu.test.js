import { shallowMount } from '@vue/test-utils';
import LeftMenu from '../../components/LeftMenu.vue'

describe('Left Menu', () => {
    const wrapper = shallowMount(LeftMenu)

    it('manipulates state', async () => {
        const $data = { 
            routes: {
                user: [
                    {name: 'test 1', path: 'test 1', show: true},
                    {name: 'test 2', path: 'test 2', show: true},
                ],
                admin: []
            }
        };

        expect(wrapper.find('.item-user').length).toBe(2);
    })
})
