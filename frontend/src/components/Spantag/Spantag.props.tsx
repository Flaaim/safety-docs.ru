import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface SpantagProps extends DetailedHTMLProps<HTMLAttributes<HTMLSpanElement>, HTMLSpanElement> {
  size?: 's' | 'm'
  children: ReactNode
}
