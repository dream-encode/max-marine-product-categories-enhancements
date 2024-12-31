import domReady from '@wordpress/dom-ready'

domReady( () => {
	document.addEventListener( 'DOMContentLoaded', () => {
		const addCategoryButton = document.querySelector( '.taxonomy-product_cat .add-term-toggle' )

		if ( addCategoryButton ) {
			addCategoryButton.style.display = 'none'
		}
	} )
} )
