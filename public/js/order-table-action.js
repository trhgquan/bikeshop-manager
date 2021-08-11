const MIN_TABLE_ITEMS = 1;
var counting = countItems;

/**
 * Add a new Item to Order.
 */
function addItem(e) {
  if (countItems === MAX_TABLE_ITEMS) {
    return false;
  }

  // Clone item
  let item = document.getElementsByName('orderInfo')[0].cloneNode(true);

  // Change attribute name
  let childNodes = item.getElementsByTagName('div')[0];
  console.log(childNodes);
  childNodes.getElementsByTagName('select')[0].setAttribute(
    'name', 
    'order_detail[' + counting + '][bike_id]'
  );
  childNodes.getElementsByTagName('input')[0].setAttribute(
    'name',
    'order_detail[' + counting + '][order_value]'
  );

  ++counting;

  // Append to end of list.
  document.getElementsByName('itemsList')[0].appendChild(item);

  ++countItems;
}

/**
 * Remove item from order.
 */
function removeItem(e) {
  // Get total items.
  let items = document.getElementsByName('orderInfo');
  
  // If this is not the only item, then remove.
  if (items.length > MIN_TABLE_ITEMS) {
    let td = e.parentNode;
    let tr = td.parentNode;
    tr.parentNode.removeChild(tr);

    --countItems;
  }
}
