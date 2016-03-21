import pygame as pg
 
def strip_from_sheet(sheet, start, size, columns, rows):
    frames = []
    for j in range(rows):
        for i in range(columns):
            location = (start[0]+size[0]*i, start[1]+size[1]*j)
            frames.append(sheet.subsurface(pg.Rect(location, size)))
    return frames
 
pg.init()
   
screen = pg.display.set_mode((800,600))
screen_rect = screen.get_rect()
done = False
 
sheet = pg.image.load('dice.png')
dice = strip_from_sheet(sheet, (0,0), (36,36), 1, 6)
 
while not done:
    for event in pg.event.get(): 
        if event.type == pg.QUIT:
            done = True
    screen.blit(dice[0], screen_rect.center)
    pg.display.update()
