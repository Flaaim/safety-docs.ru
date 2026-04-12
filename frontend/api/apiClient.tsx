interface fetchOptions extends RequestInit {
  token?: string
}

export async function apiFetch<T = void>(url: string, options: fetchOptions = {}): Promise<T> {
  const { token, headers: customHeaders, body, ...restOptions } = options;

  const headers = new Headers(customHeaders);

  if (!headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json');
  }

  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  const response = await fetch(url, {
    ...restOptions,
    headers: Object.fromEntries(headers.entries()),
    body,
  });

  if (!response.ok) {
    let errorMessage = 'Ошибка запроса';
    try {
      const errorData = await response.json();
      if (errorData && errorData.message) {
        errorMessage = errorData.message;
      }
    } catch {

    }
    throw new Error(errorMessage);
  }

  if (response.status === 204 || response.headers.get('content-length') === '0') {
    return undefined as unknown as T;
  }

  try {
    const text = await response.text();

    if (!text) {
      return undefined as unknown as T;
    }

    return JSON.parse(text) as T;
  } catch {
    return undefined as unknown as T;
  }
}
