function ready( fn ) {
  if ( document.readyState != 'loading' ){
    fn();
  } else {
    document.addEventListener( 'DOMContentLoaded', fn );
  }
}

ready( function(){
  const cookieNameValue = 'ftf-dma-notice=shown';

  if( document.getElementById( 'ftf-dma-target' ) && document.cookie.indexOf( cookieNameValue ) === -1 ){
    const note = document.getElementById( 'ftf-dma-note' );

    if ( note !== null ){
      note.style.display = 'block';

      document.getElementById( 'ftf-dma-close-btn' ).onclick = function( ev ){
        note.style.display = 'none';
        document.cookie = cookieNameValue;
      }
    }
  }
} );
