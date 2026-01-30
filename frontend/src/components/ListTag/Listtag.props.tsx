import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface ListtagProps extends DetailedHTMLProps<HTMLAttributes<HTMLOListElement>, HTMLOListElement> {
  children: ReactNode,
  appearance?: 'ol' | 'ul'
}
