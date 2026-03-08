import {JSX} from "react";
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import { Field, FieldGroup } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import styles from './DownloadButton.module.css'

export function SimpleDialog(): JSX.Element {
  return (
    <Dialog>
      <form>
        <DialogTrigger asChild>
          <Button className={styles.tag} variant="outline">Получить файлы</Button>
        </DialogTrigger>
        <DialogContent className="sm:max-w-sm">
          <DialogHeader>
            <DialogTitle>Edit profile</DialogTitle>
            <DialogDescription>
              Введите ваш адрес электронной почты:
            </DialogDescription>
          </DialogHeader>
          <FieldGroup>
            <Field>
              <Label htmlFor="name-1">Email:</Label>
              <Input id="name-1" type="email" name="name" defaultValue="" />
            </Field>
          </FieldGroup>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Отменить</Button>
            </DialogClose>
            <Button  type="submit">Получить</Button>
          </DialogFooter>
        </DialogContent>
      </form>
    </Dialog>
  )
}



