import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface HeadlineProps extends DetailedHTMLProps<HTMLAttributes<HTMLHeadingElement>, HTMLHeadingElement> {
  heading: ReactNode
}
