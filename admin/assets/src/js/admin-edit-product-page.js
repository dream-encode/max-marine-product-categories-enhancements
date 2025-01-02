import {
	createRoot
} from '@wordpress/element'

import domReady from '@wordpress/dom-ready'

import EditProductPage from './components/EditProduct/EditProduct.jsx'

domReady( () => {
	const root = createRoot( document.getElementById( 'max-marine-product-categories-enhancements-edit-product' ) )
	root.render( <EditProductPage /> )
} )