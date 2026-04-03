"use client";

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import {getToken} from "../../../../api/login";
import Cookies from "js-cookie";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
export default function LoginPage() {
  const [loading, setLoading] = useState<boolean>(false);
  const [login, setLogin] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const router = useRouter();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try{
      const data = await getToken(login, password);
      Cookies.set('admin_token', data.token, {expires: 7 });
      toast.success("Вход выполнен успешно!");
      router.push("/dashboard");
    }catch (err){
      const errorMessage = err instanceof Error ? err.message : 'Неизвестная ошибка';
      toast.error(errorMessage);
    }finally {
      setLoading(false);
    }


  };

  return (
    <div className="flex h-screen items-center justify-center bg-slate-50">
      <div className="w-full max-w-sm p-6 bg-white rounded-lg shadow-md border">
      <form onSubmit={handleLogin} className="space-y-4">
        <div className="space-y-2 text-center">
          <h1 className="text-2xl font-bold tracking-tight">Вход в панель</h1>
          <p className="text-sm text-muted-foreground">Введите данные администратора</p>
        </div>
        <div className="space-y-2">
          <Input
            placeholder="Логин"
            value={login}
            onChange={(e) => setLogin(e.target.value)}
            required
          />
        </div>
        <div className="space-y-2">
          <Input
            type="password"
            placeholder="Пароль"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        <Button type="submit" className="w-full" disabled={loading}>
          {loading ? "Вход..." : "Войти"}
        </Button>
      </form>
      </div>
    </div>
  );
}
