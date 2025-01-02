import { EDIT_PRODUCT_STORE_NAME } from '../../utils/constants'

import {
	dispatch,
	select
} from '@wordpress/data'

import { __ } from '@wordpress/i18n'

import {
	useEffect,
} from '@wordpress/element'

const EditProduct = () => {
	const eventEmitter = select( EDIT_PRODUCT_STORE_NAME ).getEventEmitter()

	useEffect( () => {
		init()
    }, [] )

	const init = () => {
		eventEmitter.on( 'validateFields', validateFields )
	}

	const validateFields = () => {
		const categoryCheckboxes = document.querySelectorAll( '#taxonomy-product_cat input[type="checkbox"]' )

		const hasCategories = Array.from( categoryCheckboxes ).some( ( checkbox ) => checkbox.checked )

		if ( ! hasCategories ) {
			dispatch( EDIT_PRODUCT_STORE_NAME ).reportError(
				'mmpce',
				__( 'You must select at least one product category before saving!', 'max-marine-product-categories-enhancements' )
			)
		}

	}
}

export default EditProduct
