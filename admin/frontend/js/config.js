(function () {
  const LOCAL_HOSTS = new Set(["localhost", "127.0.0.1", "::1", ""]);

  const trimTrailingSlash = (url) => url.replace(/\/+$/, "");

  const computeBaseFromWindow = () => {
    const { protocol, hostname, port } = window.location;
    if (!hostname) {
      return "http://localhost:4000/api/admin";
    }

    if (LOCAL_HOSTS.has(hostname.toLowerCase())) {
      const localPort = window.__ADMIN_API_PORT || 4000;
      return `${protocol}//${hostname || "localhost"}:${localPort}/api/admin`;
    }

    const explicitPort =
      window.__ADMIN_API_PORT ||
      (port && port !== "80" && port !== "443" ? port : "");
    const portSegment = explicitPort ? `:${explicitPort}` : "";
    return `${protocol}//${hostname}${portSegment}/api/admin`;
  };

  const resolveBaseUrl = () => {
    const metaTag = document.querySelector('meta[name="admin-api-base"]');
    const explicit =
      window.ADMIN_API_BASE_URL ||
      window.__ADMIN_API_BASE ||
      (metaTag ? metaTag.content : "");

    if (explicit) {
      return trimTrailingSlash(explicit);
    }

    return trimTrailingSlash(computeBaseFromWindow());
  };

  const baseUrl = resolveBaseUrl();
  window.ADMIN_API_BASE = baseUrl;
  window.__ADMIN_API_BASE = baseUrl;
  window.getAdminApiBase = () => baseUrl;
})();



