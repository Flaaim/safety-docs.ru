export default function normalizeMarkdown(text: string) {
  return text
    .replace(/\r\n/g, "\n")
    .replace(/\n\s{4,}/g, "\n");
}
