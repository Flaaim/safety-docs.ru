import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface BlockquoteProps extends DetailedHTMLProps<HTMLAttributes<HTMLQuoteElement>, HTMLQuoteElement> {
  children: ReactNode
}
