import {JSX} from "react";
import {DownloadButtonProps} from "@/components/DownloadButton/DownloadButton.props";
import {DownloadButtonClient} from "./Client/DownloadButton.client";


export const DownloadButton = ({children}:DownloadButtonProps): JSX.Element => {
  return <DownloadButtonClient>{children}</DownloadButtonClient>
}
