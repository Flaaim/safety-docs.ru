import ReactMarkdown from "react-markdown";
import remarkGfm from "remark-gfm";
import rehypeSlug from "rehype-slug";
import rehypeAutolink from "rehype-autolink-headings";

import {Ptag, Htag, Listtag} from "@/components";

type Props = {
  content: string;
};

export default function MarkdownRenderer({ content }: Props) {

  const formattedContent = (() => {
    // Проверяем, есть ли экранированные символы
    if (content.includes('\\n')) {
      return content.replace(/\\n/g, '\n');
    }
    return content;
  })();

  return (
    <ReactMarkdown
      remarkPlugins={[remarkGfm]}
      rehypePlugins={[rehypeSlug, rehypeAutolink]}
      components={{
        p: ({ children }) => <Ptag>{children}</Ptag>,
        h1: ({ children }) => <Htag tag="h1">{children}</Htag>,
        h2: ({ children }) => <Htag tag="h2">{children}</Htag>,
        h3: ({ children }) => <Htag tag="h3">{children}</Htag>,
        h4: ({ children }) => <Htag tag="h4">{children}</Htag>,
        h5: ({ children }) => <Htag tag="h5">{children}</Htag>,

        ul: ({ children }) => <Listtag appearance="ul" className="doc-list">{children}</Listtag>,
        ol: ({ children }) => <Listtag appearance="ol" className="doc-list">{children}</Listtag>,
      }}
    >
      {formattedContent}
    </ReactMarkdown>
  );
}
