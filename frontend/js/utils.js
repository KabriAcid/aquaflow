/**
 * Format a number as Nigerian Naira currency
 * @param {number} amount - The amount to format
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted currency string (e.g., "₦1,234.50")
 */
function formatNaira(amount, decimals = 2) {
  if (amount === null || amount === undefined || isNaN(amount)) {
    return "₦0.00";
  }

  const num = parseFloat(amount);
  return "₦" + num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Format a number with thousand separators
 * @param {number} num - The number to format
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted number string (e.g., "1,234.50")
 */
function formatNumber(num, decimals = 2) {
  if (num === null || num === undefined || isNaN(num)) {
    return "0.00";
  }

  const n = parseFloat(num);
  return n.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Format a date in a readable format
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string (e.g., "11/25/2024")
 */
function formatDate(date) {
  if (!date) return "";
  return new Date(date).toLocaleDateString();
}

/**
 * Format a date and time
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date and time string
 */
function formatDateTime(date) {
  if (!date) return "";
  return new Date(date).toLocaleString();
}

/**
 * Capitalize first letter of each word
 * @param {string} str - The string to capitalize
 * @returns {string} Capitalized string
 */
function capitalizeWords(str) {
  if (!str) return "";
  return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
