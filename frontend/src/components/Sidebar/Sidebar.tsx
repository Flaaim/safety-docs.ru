import {JSX} from "react";
import {SidebarProps} from "./Sidebar.props";
import classNames from "classnames";
import {SimpleSeparator} from './Separator/SimpleSeparator';
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

export const Sidebar = ({className, ...props}:SidebarProps): JSX.Element => {
  return (
    <aside {...props} className={classNames(className)}>
      <div className="grid grid-cols-1 gap-4 justify-items-center">
        <Avatar size='lg'>
          <AvatarImage src="/avatar/homer.jpg" alt='Блог охраны труда' />
          <AvatarFallback>CN</AvatarFallback>
        </Avatar>
        <SimpleSeparator />
      </div>
    </aside>);
};
