import ReactMarkdown from "react-markdown";
import remarkGfm from "remark-gfm";
import rehypeSlug from "rehype-slug";
import rehypeAutolink from "rehype-autolink-headings";

import {Ptag, Htag, Listtag} from "@/components";

type Props = {
  content: string;
};

export default function MarkdownRenderer({ content }: Props) {
  return (
    <ReactMarkdown
      remarkPlugins={[remarkGfm]}
      rehypePlugins={[rehypeSlug, rehypeAutolink]}
      components={{
        p: ({ children }) => <Ptag>{children}</Ptag>,
        h1: ({ children }) => <Htag tag="h1">{children}</Htag>,
        h2: ({ children }) => <Htag tag="h2">{children}</Htag>,
        h3: ({ children }) => <Htag tag="h3">{children}</Htag>,
        ul: ({ children }) => <Listtag className="doc-list">{children}</Listtag>,
        li: ({ children }) => <Listtag className="doc-item">{children}</Listtag>,
      }}
    >
      {content}
    </ReactMarkdown>
  );
}
