import { apiFetch } from "@api/apiClient";
import { API } from "@/app/api";
import { ParserDataInterface } from "@/interfaces/parser.interface";

export async function launchParser(
  token: string | undefined,
  parserData: ParserDataInterface
): Promise<void> {
  return await apiFetch<void>(API.parser.launch(), {
    method: "POST",
    token,
    body: JSON.stringify({
      categoryId: parserData.categoryId,
      url: parserData.url,
      amount: parserData.amount,
      cookie: parserData.cookie,
    }),
  });
}
