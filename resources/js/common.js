const nl2br = (str) => {
  var res = str.replace(/\n/g, "<br>");
  res = res.replace(/(\n)/g, "<br>");
  return res;
}

export { nl2br }