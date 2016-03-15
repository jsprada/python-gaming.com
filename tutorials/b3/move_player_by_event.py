import pygame as pg

pg.init()

class Player:
    def __init__(self, screen_rect):
        self.image = pg.image.load('spaceship.png').convert()
        self.image.set_colorkey((255,0,255))
        self.transformed_image = pg.transform.rotate(self.image, 180)
        self.rect = self.image.get_rect(center=screen_rect.center)
        self.speed = 5

    def get_event(self, event):
        if event.type == pg.KEYDOWN:
            if event.key == pg.K_LEFT:
                self.rect.x -= self.speed
            elif event.key == pg.K_RIGHT:
                self.rect.x += self.speed

    def draw(self, surf):
        surf.blit(self.transformed_image, self.rect)

screen = pg.display.set_mode((800,600))
screen_rect = screen.get_rect()
player = Player(screen_rect)
done = False
while not done:
    screen.fill((0,0,0))
    for event in pg.event.get():
        if event.type == pg.QUIT:
            done = True
        player.get_event(event)
    player.draw(screen)
    pg.display.update()
