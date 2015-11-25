# -*- coding: utf-8 -*-

a = 100
x = 0
y = 0
puts(a.to_s + "までの合計")
while x <= a
  y += x
  x += 1
end
puts(y)
