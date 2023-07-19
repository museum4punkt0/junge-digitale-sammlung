const element = document.querySelector('.load-more-container');
const button = document.querySelector('.load-more-btn');
const limit = parseInt(element.getAttribute('data-limit'));
let offset = 0;

/**
 * Fetches loadmore items via ajax call and json response
 */
const fetchPages = async () => {
  let url = `${window.location.href.split('?')[0]}`;
  let urlwithtag = url.split('/tag:');
  let firstpart = urlwithtag[0];
  let tagVar = urlwithtag[1];
  firstpart = removeChar(firstpart);
  url = `${firstpart}.json/tag:${tagVar}?offset=${offset}&limit=${limit}`;
  console.log(url); 
  try {
    const response = await fetch(url);
    const { html, more } = await response.json();
    button.hidden = !more;
    element.innerHTML += html;
    offset += limit;
  } catch (error) {
    console.log('Fetch error: ', error);
  }
}

/**
 * Removes unwanted characters in case of kirby tags in URL (please refer to kirby tags)
 * @param {string} str 
 * @returns {string}
 */
function removeChar(str) {
  var s = str.replace(/\/$/, '');
  return s;
}

// load the first pages
fetchPages();

button.addEventListener('click', fetchPages);
