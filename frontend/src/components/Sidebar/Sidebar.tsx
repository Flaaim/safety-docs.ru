import {JSX} from "react";
import {SidebarProps} from "./Sidebar.props";
import {Htag, Ptag, Spantag } from "@/components";
import classNames from "classnames";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import Link from "next/link";


export const Sidebar = ({className, ...props}:SidebarProps): JSX.Element => {
  return (
    <aside {...props} className={classNames(className)}>

      <Htag tag='h3'>Информация:</Htag>

      <Ptag >Блог охраны труда <br />
        <Spantag ><Link href="https://t.me/help_ot_news">https://t.me/help_ot_news</Link>
        </Spantag>
      </Ptag>
    </aside>)
}
