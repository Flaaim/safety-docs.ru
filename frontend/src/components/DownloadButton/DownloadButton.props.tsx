import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface DownloadButtonProps extends DetailedHTMLProps<HTMLAttributes<HTMLButtonElement>, HTMLButtonElement> {
  children: ReactNode
}
