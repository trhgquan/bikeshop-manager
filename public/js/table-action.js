const MIN_TABLE_ITEMS = 1;

/**
 * Add a new Item to Order.
 */
function addItem(e) {
  // Clone item
  let item = document
      .getElementsByName('orderInfo')[0]
      .cloneNode(true);
  
  // Append to end of list.
  let itemList = document
      .getElementsByName('itemsList')[0]
      .appendChild(item);
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
  }
}
