(function () {
  const trimTrailingSlash = (url) => url.replace(/\/+$/, "");

  const resolveBaseUrl = () => {
    if (window.ADMIN_API_BASE_URL) {
      return trimTrailingSlash(window.ADMIN_API_BASE_URL);
    }

    const metaTag = document.querySelector('meta[name="admin-api-base"]');
    if (metaTag?.content) {
      return trimTrailingSlash(metaTag.content);
    }

    return "/admin/api/admin";
  };

  const baseUrl = resolveBaseUrl();

  window.ADMIN_API_BASE = baseUrl;
  window.getAdminApiBase = () => baseUrl;
})();
