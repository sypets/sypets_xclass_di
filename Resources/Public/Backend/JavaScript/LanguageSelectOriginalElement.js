/**
 * selektiert das deutsche Ursprungselement des übersetzten Elements
 * Wird aus dem "Sprachlich verbunden"-Link im Element-Footer englischer Elemente in der Sprachen-Ansicht aufgerufen
 * @param uid UID des deutschen Elements
 *
 * s. PHP Klassse \Uniol\Unioltemplate\Backend\PreviewRenderer\StandardContentPreviewRenderer
 */
function selectOriginalElement(uid) {
  var iframe = document.getElementById('typo3-contentIframe');
  var innerDoc = window.document; // = iframe.contentDocument || iframe.contentWindow.document;
  alt = innerDoc.getElementsByClassName('border');
  if(alt.length) { alt[0].classList.remove('border'); }
  alt = innerDoc.getElementsByClassName('border-primary');
  if(alt.length) { alt[0].classList.remove('border-primary'); }
  el = innerDoc.getElementById('element-tt_content-'+uid);
  el.classList.add('border');
  el.classList.add('border-primary');
  el.scrollIntoView(false);
  console.log('selectOriginalElement');
}

