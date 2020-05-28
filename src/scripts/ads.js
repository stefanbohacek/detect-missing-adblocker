function ready( fn ) {
  if ( document.readyState != 'loading' ){
    fn();
  } else {
    document.addEventListener( 'DOMContentLoaded', fn );
  }
}

ready( function(){
  const e = document.createElement( 'div' );
  e.id = 'ftf-detect-missing-adblocker';
  e.style.display = 'none';
  document.body.appendChild( e );
} );
