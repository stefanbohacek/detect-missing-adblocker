const ready = (fn) => {
  if (document.readyState != "loading") {
    fn();
  } else {
    document.addEventListener("DOMContentLoaded", fn);
  }
}

ready(() => {
  const cookieNameValue = "ftf-dma-notice=shown";

  if (document.cookie.indexOf(cookieNameValue) === -1) {
    const note = document.getElementById("ftf-dma-note");

    if (note !== null) {
      note.classList.remove('d-none');

      document.getElementById("ftf-dma-close-btn").onclick = (ev) => {
        note.classList.add('d-none');
        document.cookie = cookieNameValue;
      };
    }
  }
});
