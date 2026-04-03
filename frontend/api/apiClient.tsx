interface fetchOptions extends RequestInit {
  token?: string
}

export async function apiFetch<T = void>(url: string, options: FetchOptions = {}): Promise<T> {
  const { token, headers: customHeaders, body, ...restOptions } = options;

  const headers: HeadersInit = {
    ...customHeaders
  };

  if (!(body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
  }

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(url, {
    headers,
    body,
    ...restOptions
  });

  if (!response.ok) {
    let errorMessage = 'Ошибка запроса';
    try {
      const errorData = await response.json();
      if (errorData && errorData.message) {
        errorMessage = errorData.message;
      }
    } catch (e) {
      console.error("Не удалось распарсить JSON ошибки:", e);
    }
    throw new Error(errorMessage);
  }

  if (response.status === 204 || response.headers.get('content-length') === '0') {
    return undefined as unknown as T;
  }

  try {
    const text = await response.text();
    return text ? JSON.parse(text) : undefined;
  } catch (error) {
    return undefined as unknown as T;
  }
}
